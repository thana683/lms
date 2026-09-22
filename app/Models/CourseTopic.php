<?php

namespace App\Models;

use App\Enums\ContentMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CourseTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_term_id', 'day_number', 'title', 'content_type',
        'content_url', 'has_quiz', 'quiz_pass_score', 'requires_practice_log', 'order_number',
    ];

    protected $casts = [
        'content_type' => ContentMode::class,
        'has_quiz' => 'boolean',
        'requires_practice_log' => 'boolean',
    ];

    public function term(): BelongsTo
    {
        return $this->belongsTo(CourseTerm::class, 'course_term_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(TopicProgress::class);
    }

    public function submissions(): MorphMany
    {
        return $this->morphMany(FormSubmission::class, 'submittable');
    }
}
