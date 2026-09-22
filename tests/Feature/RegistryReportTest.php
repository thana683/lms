<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CourseOffering;
use App\Models\User;
use App\Models\UserLicense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistryReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['system_admin', 'central_admin', 'branch_admin', 'user'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    public function test_branch_admin_only_sees_their_own_branchs_history(): void
    {
        $branch = Branch::factory()->create();
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $admin->assignRole('branch_admin');

        $ownOffering = CourseOffering::factory()->create(['branch_id' => $branch->id]);
        $ownLicense = UserLicense::factory()->create(['course_offering_id' => $ownOffering->id]);

        $otherLicense = UserLicense::factory()->create();

        $response = $this->actingAs($admin)->get(route('reports.enrollments'));

        $response->assertOk();
        $response->assertSee($ownLicense->user->name);
        $response->assertDontSee($otherLicense->user->name);
    }

    public function test_central_admin_sees_every_branchs_history(): void
    {
        $central = User::factory()->create();
        $central->assignRole('central_admin');

        $licenseA = UserLicense::factory()->create();
        $licenseB = UserLicense::factory()->create();

        $response = $this->actingAs($central)->get(route('reports.enrollments'));

        $response->assertSee($licenseA->user->name);
        $response->assertSee($licenseB->user->name);
    }

    public function test_export_streams_a_csv(): void
    {
        $central = User::factory()->create();
        $central->assignRole('central_admin');
        UserLicense::factory()->create();

        $response = $this->actingAs($central)->get(route('reports.enrollments.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_plain_user_cannot_view_reports(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)->get(route('reports.enrollments'))->assertForbidden();
    }
}
