<?php

namespace App\Models;

use App\Enums\ExamType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = ['course_term_id', 'type', 'question_count', 'pass_score', 'max_attempts'];

    protected $casts = [
        'type' => ExamType::class,
    ];

    public function term(): BelongsTo
    {
        return $this->belongsTo(CourseTerm::class, 'course_term_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * §1.5.2/§1.5.3: enforces the retry cooldown and attempt cap. Returns a Thai
     * reason if the license can't attempt this exam right now, null if it can.
     */
    public function blockReasonFor(UserLicense $license): ?string
    {
        $attempts = $this->attempts()->where('user_license_id', $license->id)->get();

        if ($attempts->isEmpty()) {
            return null;
        }

        $latest = $attempts->sortByDesc('attempt_number')->first();

        if ($latest->passed === true) {
            return 'สอบผ่านแล้ว';
        }

        if ($this->max_attempts && $attempts->count() >= $this->max_attempts) {
            return 'ใช้สิทธิ์สอบครบตามจำนวนที่กำหนดแล้ว';
        }

        if ($latest->passed === null) {
            return 'รอผลการตรวจข้อสอบ';
        }

        if ($latest->next_retry_at && now()->lt($latest->next_retry_at)) {
            return 'ต้องรอถึง '.$latest->next_retry_at->format('d/m/Y H:i').' จึงจะสอบใหม่ได้';
        }

        return null;
    }
}
