<?php

namespace Database\Factories;

use App\Enums\ContentMode;
use App\Models\CourseTerm;
use App\Models\CourseTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseTopic>
 */
class CourseTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_term_id' => CourseTerm::factory(),
            'title' => $this->faker->sentence(3),
            'content_type' => ContentMode::Video,
            'has_quiz' => false,
            'requires_practice_log' => false,
        ];
    }
}
