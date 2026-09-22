<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicProgress extends Model
{
    protected $fillable = [
        'user_license_id', 'course_topic_id', 'completed_at', 'quiz_score', 'quiz_passed', 'last_seen_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'quiz_passed' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(UserLicense::class, 'user_license_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(CourseTopic::class, 'course_topic_id');
    }

    /**
     * §1.4.2/§1.4.3: a topic only counts as done once its quiz (if any) is passed
     * and its practice log (if required) is submitted.
     */
    public function refreshCompletion(): void
    {
        $topic = $this->topic;

        $quizOk = ! $topic->has_quiz || $this->quiz_passed;
        $practiceOk = ! $topic->requires_practice_log || $topic->submissions()
            ->where('user_id', $this->license->user_id)
            ->exists();

        if ($quizOk && $practiceOk && ! $this->completed_at) {
            $this->update(['completed_at' => now()]);
        }
    }
}
