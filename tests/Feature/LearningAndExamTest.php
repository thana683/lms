<?php

namespace Tests\Feature;

use App\Jobs\SendExamResultToEcert;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseTerm;
use App\Models\CourseTopic;
use App\Models\User;
use App\Models\UserLicense;
use App\Services\EcertClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LearningAndExamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['system_admin', 'central_admin', 'branch_admin', 'user'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    /**
     * @return array{0: User, 1: CourseTerm, 2: UserLicense}
     */
    private function enrolledLearner(): array
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $term = CourseTerm::factory()->create(['course_id' => $course->id]);
        $offering = CourseOffering::factory()->create(['course_id' => $course->id]);
        $license = $offering->assignLicenseTo($user);

        return [$user, $term, $license];
    }

    public function test_visiting_a_plain_topic_marks_it_complete(): void
    {
        [$user, $term] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create(['course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => false]);

        $this->actingAs($user)->get(route('topics.show', $topic))->assertOk();

        $this->assertNotNull($topic->progress()->first()->completed_at);
    }

    public function test_a_topic_with_a_quiz_only_completes_after_passing(): void
    {
        [$user, $term] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create([
            'course_term_id' => $term->id, 'has_quiz' => true, 'quiz_pass_score' => 60, 'requires_practice_log' => false,
        ]);
        $this->actingAs($user)->get(route('topics.show', $topic));

        $this->actingAs($user)->post(route('topics.quiz', $topic), ['quiz_score' => 40]);
        $this->assertNull($topic->progress()->first()->completed_at);

        $this->actingAs($user)->post(route('topics.quiz', $topic), ['quiz_score' => 80]);
        $this->assertNotNull($topic->progress()->first()->fresh()->completed_at);
    }

    public function test_a_topic_requiring_a_practice_log_completes_after_submission(): void
    {
        [$user, $term] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create([
            'course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => true,
        ]);
        $this->actingAs($user)->get(route('topics.show', $topic));
        $this->assertNull($topic->progress()->first()->completed_at);

        $this->actingAs($user)->post(route('topics.practice-log', $topic), [
            'activity' => 'sitting',
            'duration_seconds' => 600,
        ]);

        $this->assertNotNull($topic->progress()->first()->fresh()->completed_at);
    }

    public function test_exam_is_blocked_until_all_topics_in_the_term_are_complete(): void
    {
        [$user, $term, $license] = $this->enrolledLearner();
        CourseTopic::factory()->count(2)->create(['course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => false]);
        $exam = $term->exams()->create(['type' => 'objective', 'pass_score' => 50]);

        $response = $this->actingAs($user)->get(route('exams.show', $term));
        $response->assertSee('ต้องเรียนให้ครบทุกหัวข้อก่อนจึงจะสอบได้');

        $this->actingAs($user)->post(route('exam-attempts.store', $exam), ['score' => 90])->assertStatus(422);
    }

    public function test_passing_the_objective_exam_completes_the_license(): void
    {
        Queue::fake();

        [$user, $term, $license] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create(['course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => false]);
        $this->actingAs($user)->get(route('topics.show', $topic));
        $exam = $term->exams()->create(['type' => 'objective', 'pass_score' => 50]);

        $this->actingAs($user)
            ->post(route('exam-attempts.store', $exam), ['score' => 80])
            ->assertRedirect();

        $this->assertSame('completed', $license->fresh()->status->value);
        $this->assertDatabaseHas('exam_attempts', ['exam_id' => $exam->id, 'passed' => true, 'ecert_eligible' => true]);
        Queue::assertPushed(SendExamResultToEcert::class);
    }

    public function test_failing_the_objective_exam_sets_a_24_hour_retry_cooldown(): void
    {
        [$user, $term] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create(['course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => false]);
        $this->actingAs($user)->get(route('topics.show', $topic));
        $exam = $term->exams()->create(['type' => 'objective', 'pass_score' => 50]);

        $this->actingAs($user)->post(route('exam-attempts.store', $exam), ['score' => 30]);

        $this->actingAs($user)
            ->post(route('exam-attempts.store', $exam), ['score' => 90])
            ->assertStatus(422);
    }

    public function test_license_is_revoked_once_max_attempts_are_exhausted_without_a_pass(): void
    {
        [$user, $term, $license] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create(['course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => false]);
        $this->actingAs($user)->get(route('topics.show', $topic));
        $exam = $term->exams()->create(['type' => 'objective', 'pass_score' => 50, 'max_attempts' => 1]);

        $this->actingAs($user)->post(route('exam-attempts.store', $exam), ['score' => 20]);

        $this->assertSame('revoked', $license->fresh()->status->value);
    }

    public function test_subjective_exam_awaits_admin_grading_then_can_pass(): void
    {
        [$user, $term, $license] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create(['course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => false]);
        $this->actingAs($user)->get(route('topics.show', $topic));
        $exam = $term->exams()->create(['type' => 'subjective', 'pass_score' => 50]);

        $this->actingAs($user)->post(route('exam-attempts.store', $exam), [
            'answer_file' => UploadedFile::fake()->create('answer.pdf', 50, 'application/pdf'),
        ]);

        $this->actingAs($user)->get(route('exams.show', $term))->assertSee('รอผลการตรวจข้อสอบ');

        $admin = User::factory()->create();
        $admin->assignRole('central_admin');
        $attempt = $exam->attempts()->first();

        $this->actingAs($admin)
            ->patch(route('exam-attempts.grade', $attempt), ['score' => 70])
            ->assertRedirect();

        $this->assertSame('completed', $license->fresh()->status->value);
    }

    public function test_ecert_job_stamps_the_attempt_once_sent(): void
    {
        [$user, $term] = $this->enrolledLearner();
        $topic = CourseTopic::factory()->create(['course_term_id' => $term->id, 'has_quiz' => false, 'requires_practice_log' => false]);
        $this->actingAs($user)->get(route('topics.show', $topic));
        $exam = $term->exams()->create(['type' => 'objective', 'pass_score' => 50]);
        $this->actingAs($user)->post(route('exam-attempts.store', $exam), ['score' => 80]);
        $attempt = $exam->attempts()->first();

        (new SendExamResultToEcert($attempt))->handle(new EcertClient);

        $this->assertNotNull($attempt->fresh()->ecert_sent_at);
    }
}
