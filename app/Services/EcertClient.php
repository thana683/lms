<?php

namespace App\Services;

use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Log;

/**
 * §1.5.4: hand off a passed exam result to the external E-Cert system.
 *
 * ponytail: no real E-Cert endpoint/credentials exist yet, so send() only
 * logs the payload as a mock call. Swap the body for an HTTP client call
 * (config('services.ecert.url'), auth token, response handling) once the
 * real API is available — SendExamResultToEcert already calls this through
 * the queue, so nothing else needs to change.
 */
class EcertClient
{
    public function send(ExamAttempt $attempt): void
    {
        Log::info('ecert.mock_send', [
            'exam_attempt_id' => $attempt->id,
            'user_id' => $attempt->license->user_id,
            'course_id' => $attempt->license->offering->course_id,
            'score' => $attempt->score,
        ]);
    }
}
