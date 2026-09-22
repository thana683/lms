<?php

namespace Database\Factories;

use App\Enums\OfferingStatus;
use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseOffering>
 */
class CourseOfferingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'branch_id' => Branch::factory(),
            'requested_by' => User::factory(),
            'status' => OfferingStatus::Approved,
            'quota' => 10,
            'start_date' => now()->toDateString(),
        ];
    }
}
