<?php

namespace App\Models;

use App\Enums\LicenseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class UserLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_offering_id', 'status', 'assigned_by', 'assigned_at', 'revoked_at',
    ];

    protected $casts = [
        'status' => LicenseStatus::class,
        'assigned_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function activeFor(User $user, Course $course): ?self
    {
        return $user->licenses()
            ->where('status', LicenseStatus::Active)
            ->whereHas('offering', fn ($q) => $q->where('course_id', $course->id))
            ->first();
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function topicProgress(): HasMany
    {
        return $this->hasMany(TopicProgress::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * §1.3.1 step two: branch admin approves a self-registration request.
     */
    public function approve(User $approver): void
    {
        if ($this->status !== LicenseStatus::Pending) {
            throw new RuntimeException('คำขอนี้ถูกพิจารณาไปแล้ว');
        }

        if ($this->offering->remainingQuota() <= 0) {
            throw new RuntimeException('โควตาของหลักสูตรนี้เต็มแล้ว');
        }

        $this->update([
            'status' => LicenseStatus::Active,
            'assigned_by' => $approver->id,
            'assigned_at' => now(),
        ]);
    }

    public function reject(): void
    {
        if ($this->status !== LicenseStatus::Pending) {
            throw new RuntimeException('คำขอนี้ถูกพิจารณาไปแล้ว');
        }

        $this->update(['status' => LicenseStatus::Rejected]);
    }

    public function revoke(): void
    {
        $this->update(['status' => LicenseStatus::Revoked, 'revoked_at' => now()]);
    }

    /**
     * §1.5.5: once every term's exam is passed, access ends via the
     * `completed` status (activeFor() only matches `active`), same effect
     * as revoke() but distinguishes a pass from a failed/expired license.
     */
    public function refreshCompletionStatus(): void
    {
        if ($this->status !== LicenseStatus::Active) {
            return;
        }

        $exams = Exam::whereIn('course_term_id', $this->offering->course->terms()->pluck('id'))->get();

        if ($exams->isEmpty()) {
            return;
        }

        $allPassed = $exams->every(
            fn (Exam $exam) => $this->examAttempts()->where('exam_id', $exam->id)->where('passed', true)->exists()
        );

        if ($allPassed) {
            $this->update(['status' => LicenseStatus::Completed]);
        }
    }
}
