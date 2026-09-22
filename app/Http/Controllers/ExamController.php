<?php

namespace App\Http\Controllers;

use App\Enums\ExamType;
use App\Jobs\SendExamResultToEcert;
use App\Models\CourseTerm;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\UserLicense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * §1.1/§1.5.1: central/system admin defines one final exam per term.
     * ponytail: single exam per term — the spec allows multiple sets, add a
     * picker once more than one is actually needed.
     */
    public function storeForTerm(Request $request, CourseTerm $term): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::enum(ExamType::class)],
            'question_count' => ['nullable', 'integer', 'min:1'],
            'pass_score' => ['required', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
        ]);

        $term->exams()->create($data);

        return back()->with('status', 'เพิ่มแบบทดสอบท้ายเทอมเรียบร้อยแล้ว');
    }

    public function show(CourseTerm $term): View
    {
        $license = UserLicense::activeFor(Auth::user(), $term->course);
        abort_if($license === null, 403, 'คุณไม่มีสิทธิ์เข้าเรียนหลักสูตรนี้');

        $exam = $term->exams()->first();
        abort_if($exam === null, 404, 'เทอมนี้ยังไม่มีแบบทดสอบ');

        $eligible = $term->isEligibleForExamFor($license);
        $blockReason = $eligible ? $exam->blockReasonFor($license) : 'ต้องเรียนให้ครบทุกหัวข้อก่อนจึงจะสอบได้';
        $attempts = $exam->attempts()->where('user_license_id', $license->id)->orderByDesc('attempt_number')->get();

        return view('exams.show', compact('term', 'exam', 'blockReason', 'attempts'));
    }

    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $license = UserLicense::activeFor(Auth::user(), $exam->term->course);
        abort_if($license === null, 403, 'คุณไม่มีสิทธิ์เข้าเรียนหลักสูตรนี้');
        abort_unless($exam->term->isEligibleForExamFor($license), 422, 'ต้องเรียนให้ครบทุกหัวข้อก่อนจึงจะสอบได้');

        $blockReason = $exam->blockReasonFor($license);
        abort_if($blockReason, 422, $blockReason);

        $attemptNumber = $exam->attempts()->where('user_license_id', $license->id)->max('attempt_number') + 1;

        if ($exam->type === ExamType::Objective) {
            $data = $request->validate(['score' => ['required', 'integer', 'min:0', 'max:100']]);
            $passed = $data['score'] >= $exam->pass_score;

            $attempt = $exam->attempts()->create([
                'user_license_id' => $license->id,
                'attempt_number' => $attemptNumber,
                'score' => $data['score'],
                'passed' => $passed,
                'taken_at' => now(),
                'next_retry_at' => $passed ? null : now()->addDay(),
                'ecert_eligible' => $passed,
            ]);
        } else {
            $request->validate(['answer_file' => ['required', 'file', 'max:10240']]);

            $attempt = $exam->attempts()->create([
                'user_license_id' => $license->id,
                'attempt_number' => $attemptNumber,
                'answer_path' => $request->file('answer_file')->store('exam-answers', 'local'),
                'taken_at' => now(),
            ]);
        }

        if ($attempt->passed) {
            $license->refreshCompletionStatus();
            SendExamResultToEcert::dispatch($attempt);
        } elseif ($attempt->passed === false) {
            $this->revokeIfAttemptsExhausted($exam, $license);
        }

        return redirect()->route('exams.show', $exam->term)->with('status', 'ส่งคำตอบเรียบร้อยแล้ว');
    }

    /**
     * §1.5.3: subjective exams are graded manually by an admin.
     */
    public function grade(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $data = $request->validate(['score' => ['required', 'integer', 'min:0', 'max:100']]);
        $passed = $data['score'] >= $attempt->exam->pass_score;

        $attempt->update([
            'score' => $data['score'],
            'passed' => $passed,
            'graded_by' => Auth::id(),
            'graded_at' => now(),
            'next_retry_at' => $passed ? null : now()->addWeek(),
            'ecert_eligible' => $passed,
        ]);

        if ($passed) {
            $attempt->license->refreshCompletionStatus();
            SendExamResultToEcert::dispatch($attempt);
        } else {
            $this->revokeIfAttemptsExhausted($attempt->exam, $attempt->license);
        }

        return back()->with('status', 'บันทึกผลการตรวจข้อสอบเรียบร้อยแล้ว');
    }

    /**
     * §1.5.5: once an exam's retry limit is exhausted without a pass, access ends.
     * ponytail: only fires when max_attempts is actually set — with unlimited
     * retries "the exam ends" has no natural trigger to hook this on.
     */
    private function revokeIfAttemptsExhausted(Exam $exam, UserLicense $license): void
    {
        if (! $exam->max_attempts) {
            return;
        }

        $attempts = $exam->attempts()->where('user_license_id', $license->id)->get();

        if ($attempts->count() >= $exam->max_attempts && ! $attempts->contains('passed', true)) {
            $license->revoke();
        }
    }
}
