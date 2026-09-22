<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['system_admin', 'central_admin', 'user'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    public function test_plain_user_cannot_access_course_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)->get(route('courses.index'))->assertForbidden();
    }

    public function test_central_admin_can_create_a_course(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('central_admin');

        $response = $this->actingAs($admin)->post(route('courses.store'), [
            'name' => 'หลักสูตรครูสมาธิ',
            'schedule_type' => 'once',
            'content_mode' => 'video',
            'branch_requirement' => 'none',
        ]);

        $course = Course::first();
        $response->assertRedirect(route('courses.show', $course));
        $this->assertSame($admin->id, $course->created_by);
    }

    public function test_admin_can_add_a_term_and_topic_to_a_course(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('system_admin');
        $course = Course::factory()->create();

        $this->actingAs($admin)->post(route('course-terms.store', $course), [
            'name' => 'เทอม 1',
        ])->assertRedirect();

        $term = CourseTerm::first();
        $this->assertSame('เทอม 1', $term->name);

        $this->actingAs($admin)->post(route('course-topics.store', $term), [
            'title' => 'บทที่ 1',
            'content_type' => 'video',
            'has_quiz' => '1',
            'quiz_pass_score' => 60,
        ])->assertRedirect();

        $this->assertDatabaseHas('course_topics', [
            'course_term_id' => $term->id,
            'title' => 'บทที่ 1',
            'has_quiz' => true,
            'quiz_pass_score' => 60,
        ]);
    }
}
