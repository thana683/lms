<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseTerm>
 */
class CourseTermFactory extends Factory
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
            'name' => 'เทอม '.$this->faker->numberBetween(1, 4),
        ];
    }
}
