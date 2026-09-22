<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseTerm extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'name', 'order_number'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(CourseTopic::class)->orderBy('order_number');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function completedTopicCountFor(UserLicense $license): int
    {
        return TopicProgress::where('user_license_id', $license->id)
            ->whereIn('course_topic_id', $this->topics()->pluck('id'))
            ->whereNotNull('completed_at')
            ->count();
    }

    /**
     * §1.4.5/§1.5: must finish every topic in the term before the exam unlocks.
     * ponytail: fixed "all topics" rule — add a per-term required-count column if
     * a partial-completion threshold is ever needed.
     */
    public function isEligibleForExamFor(UserLicense $license): bool
    {
        $total = $this->topics()->count();

        return $total > 0 && $this->completedTopicCountFor($license) >= $total;
    }
}
