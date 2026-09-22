<?php

namespace Tests\Feature;

use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CourseOfferingQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigning_a_license_within_quota_succeeds(): void
    {
        $offering = CourseOffering::factory()->create(['quota' => 1]);

        $license = $offering->assignLicenseTo(User::factory()->create());

        $this->assertSame(0, $offering->fresh()->remainingQuota());
        $this->assertSame('active', $license->status->value);
    }

    public function test_assigning_a_license_past_quota_fails(): void
    {
        $offering = CourseOffering::factory()->create(['quota' => 1]);
        $offering->assignLicenseTo(User::factory()->create());

        $this->expectException(RuntimeException::class);

        $offering->assignLicenseTo(User::factory()->create());
    }
}
