<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id', 'user_license_id', 'attempt_number', 'score', 'answer_path', 'passed',
        'graded_by', 'taken_at', 'graded_at', 'next_retry_at', 'ecert_eligible', 'ecert_sent_at',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'ecert_eligible' => 'boolean',
        'taken_at' => 'datetime',
        'graded_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'ecert_sent_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(UserLicense::class, 'user_license_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
