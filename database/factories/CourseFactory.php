<?php

namespace Database\Factories;

use App\Enums\BranchRequirement;
use App\Enums\ContentMode;
use App\Enums\ScheduleType;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'schedule_type' => ScheduleType::Once,
            'content_mode' => ContentMode::Video,
            'branch_requirement' => BranchRequirement::None,
        ];
    }
}
