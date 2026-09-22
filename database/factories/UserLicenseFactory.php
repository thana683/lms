<?php

namespace Database\Factories;

use App\Enums\LicenseStatus;
use App\Models\CourseOffering;
use App\Models\User;
use App\Models\UserLicense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserLicense>
 */
class UserLicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_offering_id' => CourseOffering::factory(),
            'status' => LicenseStatus::Active,
        ];
    }
}
