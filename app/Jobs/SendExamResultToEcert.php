<?php

namespace App\Jobs;

use App\Models\ExamAttempt;
use App\Services\EcertClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * §1.5.4: within 3 days of a pass, hand the result off to E-Cert.
 */
class SendExamResultToEcert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ExamAttempt $attempt) {}

    public function handle(EcertClient $client): void
    {
        $client->send($this->attempt);

        $this->attempt->update(['ecert_sent_at' => now()]);
    }
}
