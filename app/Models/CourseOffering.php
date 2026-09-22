<?php

namespace App\Models;

use App\Enums\BranchRequirement;
use App\Enums\LicenseStatus;
use App\Enums\OfferingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class CourseOffering extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'branch_id', 'requested_by', 'approved_by', 'status',
        'quota', 'start_date', 'end_date', 'session_dates', 'attachment_path', 'decided_at',
    ];

    protected $casts = [
        'status' => OfferingStatus::class,
        'session_dates' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'decided_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(UserLicense::class);
    }

    public function remainingQuota(): int
    {
        return $this->quota - $this->licenses()
            ->whereIn('status', [LicenseStatus::Active, LicenseStatus::Completed])
            ->count();
    }

    /**
     * Grant a license (Assign User License, §1.3.2/§1.3.3) against this offering's quota.
     * Reuses a prior rejected/revoked row for the same user instead of hitting the
     * unique(user_id, course_offering_id) constraint.
     */
    public function assignLicenseTo(User $user, ?User $assignedBy = null): UserLicense
    {
        $existing = $this->licenses()->where('user_id', $user->id)->first();

        if ($existing && in_array($existing->status, [LicenseStatus::Active, LicenseStatus::Completed, LicenseStatus::Pending], true)) {
            throw new RuntimeException('ผู้ใช้นี้ลงทะเบียนหลักสูตรนี้อยู่แล้ว');
        }

        if ($this->remainingQuota() <= 0) {
            throw new RuntimeException('โควตาของหลักสูตรนี้เต็มแล้ว');
        }

        $attributes = [
            'status' => LicenseStatus::Active,
            'assigned_by' => $assignedBy?->id,
            'assigned_at' => now(),
            'revoked_at' => null,
        ];

        if ($existing) {
            $existing->update($attributes);

            return $existing;
        }

        return $this->licenses()->create(['user_id' => $user->id, ...$attributes]);
    }

    /**
     * Self-registration eligibility (§1.3.1). Returns a Thai reason if ineligible, null if eligible.
     */
    public function eligibilityErrorFor(User $user): ?string
    {
        if ($this->status !== OfferingStatus::Approved) {
            return 'หลักสูตรนี้ยังไม่เปิดรับสมัคร';
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return 'หมดเขตรับสมัครแล้ว';
        }

        $requirement = $this->course->branch_requirement;

        if ($requirement !== BranchRequirement::None && $user->branch_id === null) {
            return 'ต้องมีสาขาที่สังกัดจึงจะสมัครได้';
        }

        if ($requirement === BranchRequirement::RequiredOwn && $user->branch_id !== $this->branch_id) {
            return 'หลักสูตรนี้รับสมัครเฉพาะผู้ที่สังกัดสาขานี้';
        }

        if ($this->course->prerequisite_course_id) {
            $passedPrerequisite = UserLicense::where('user_id', $user->id)
                ->where('status', LicenseStatus::Completed)
                ->whereHas('offering', fn ($q) => $q->where('course_id', $this->course->prerequisite_course_id))
                ->exists();

            if (! $passedPrerequisite) {
                return 'ต้องผ่านหลักสูตรที่กำหนดไว้ก่อนจึงจะลงทะเบียนได้';
            }
        }

        $alreadyRegistered = $this->licenses()
            ->where('user_id', $user->id)
            ->whereNotIn('status', [LicenseStatus::Rejected, LicenseStatus::Revoked])
            ->exists();

        if ($alreadyRegistered) {
            return 'คุณลงทะเบียนหลักสูตรนี้ไปแล้ว';
        }

        return null;
    }
}
