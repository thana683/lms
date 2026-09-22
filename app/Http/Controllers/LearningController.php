<?php

namespace App\Http\Controllers;

use App\Models\CourseTerm;
use App\Models\CourseTopic;
use App\Models\FormDefinition;
use App\Models\TopicProgress;
use App\Models\UserLicense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LearningController extends Controller
{
    /**
     * §1.4/§1.4.5: a term's topic list with progress and the "N topics left" count.
     */
    public function term(CourseTerm $term): View
    {
        $license = $this->licenseFor($term);

        $term->load('topics', 'exams');
        $progress = TopicProgress::where('user_license_id', $license->id)
            ->whereIn('course_topic_id', $term->topics->pluck('id'))
            ->get()
            ->keyBy('course_topic_id');

        $completedCount = $progress->whereNotNull('completed_at')->count();
        $totalCount = $term->topics->count();
        $eligibleForExam = $term->isEligibleForExamFor($license);

        return view('learning.term', compact('term', 'progress', 'completedCount', 'totalCount', 'eligibleForExam', 'license'));
    }

    public function topic(CourseTopic $topic): View
    {
        $license = $this->licenseFor($topic->term);

        $progress = TopicProgress::firstOrCreate(
            ['user_license_id' => $license->id, 'course_topic_id' => $topic->id]
        );
        $progress->refreshCompletion();

        $practiceSubmitted = $topic->requires_practice_log
            ? $topic->submissions()->where('user_id', Auth::id())->exists()
            : null;

        return view('learning.topic', compact('topic', 'progress', 'practiceSubmitted'));
    }

    public function heartbeat(CourseTopic $topic): RedirectResponse
    {
        $license = $this->licenseFor($topic->term);

        TopicProgress::updateOrCreate(
            ['user_license_id' => $license->id, 'course_topic_id' => $topic->id],
            ['last_seen_at' => now()]
        );

        return back();
    }

    /**
     * §1.4.2: self-reported quiz score — there is no question bank yet, so the
     * learner enters the score they got and it's checked against quiz_pass_score.
     * ponytail: swap for a real question/answer engine once one exists.
     */
    public function submitQuiz(Request $request, CourseTopic $topic): RedirectResponse
    {
        abort_unless($topic->has_quiz, 404);

        $data = $request->validate([
            'quiz_score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $license = $this->licenseFor($topic->term);

        $progress = TopicProgress::firstOrCreate(
            ['user_license_id' => $license->id, 'course_topic_id' => $topic->id]
        );

        $progress->update([
            'quiz_score' => $data['quiz_score'],
            'quiz_passed' => $data['quiz_score'] >= $topic->quiz_pass_score,
        ]);

        $progress->refreshCompletion();

        return back()->with('status', $progress->quiz_passed ? 'ทำแบบทดสอบผ่านแล้ว' : 'ยังไม่ผ่านเกณฑ์ ลองใหม่อีกครั้ง');
    }

    /**
     * §1.4.3/§1.4.4: บันทึกผลการปฏิบัติ (เดินจงกรม/นั่งสมาธิ) พร้อมเวลาและคะแนนการบ้าน.
     */
    public function submitPracticeLog(Request $request, CourseTopic $topic): RedirectResponse
    {
        abort_unless($topic->requires_practice_log, 404);

        $data = $request->validate([
            'activity' => ['required', 'in:walking,sitting'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'homework_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $license = $this->licenseFor($topic->term);

        $form = FormDefinition::firstOrCreate(
            ['type' => 'practice_log'],
            ['name' => 'บันทึกผลปฏิบัติ', 'schema' => ['activity', 'duration_seconds', 'notes', 'homework_score']]
        );

        $topic->submissions()->create([
            'form_definition_id' => $form->id,
            'user_id' => Auth::id(),
            'answers' => $data,
            'submitted_at' => now(),
        ]);

        $progress = TopicProgress::firstOrCreate(
            ['user_license_id' => $license->id, 'course_topic_id' => $topic->id]
        );
        $progress->refreshCompletion();

        return back()->with('status', 'บันทึกผลปฏิบัติเรียบร้อยแล้ว');
    }

    private function licenseFor(CourseTerm $term): UserLicense
    {
        $license = UserLicense::activeFor(Auth::user(), $term->course);

        abort_if($license === null, 403, 'คุณไม่มีสิทธิ์เข้าเรียนหลักสูตรนี้');

        return $license;
    }
}
