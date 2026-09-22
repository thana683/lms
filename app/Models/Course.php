<?php

namespace App\Models;

use App\Enums\BranchRequirement;
use App\Enums\ContentMode;
use App\Enums\ScheduleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'schedule_type', 'schedule_rule', 'content_mode',
        'branch_requirement', 'prerequisite_course_id', 'pass_criteria', 'created_by',
    ];

    protected $casts = [
        'schedule_type' => ScheduleType::class,
        'content_mode' => ContentMode::class,
        'branch_requirement' => BranchRequirement::class,
        'schedule_rule' => 'array',
    ];

    public function prerequisite(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'prerequisite_course_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function terms(): HasMany
    {
        return $this->hasMany(CourseTerm::class)->orderBy('order_number');
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }
}
