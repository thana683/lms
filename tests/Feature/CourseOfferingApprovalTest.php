<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseOfferingApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['system_admin', 'central_admin', 'branch_admin', 'user'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function branchAdmin(): User
    {
        $branch = Branch::factory()->create();
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $admin->assignRole('branch_admin');

        return $admin;
    }

    public function test_a_user_without_a_branch_cannot_request_to_open_a_course(): void
    {
        $admin = User::factory()->create(['branch_id' => null]);
        $admin->assignRole('branch_admin');

        $this->actingAs($admin)->get(route('offerings.create'))->assertForbidden();
    }

    public function test_branch_admin_can_submit_a_request_with_signed_attachment(): void
    {
        Storage::fake('local');
        $admin = $this->branchAdmin();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->post(route('offerings.store'), [
            'course_id' => $course->id,
            'start_date' => now()->addDays(7)->toDateString(),
            'quota' => 20,
            'attachment' => UploadedFile::fake()->create('approval.pdf', 100, 'application/pdf'),
        ]);

        $offering = CourseOffering::first();
        $response->assertRedirect(route('offerings.show', $offering));
        $this->assertSame('pending', $offering->status->value);
        $this->assertSame($admin->branch_id, $offering->branch_id);
        Storage::disk('local')->assertExists($offering->attachment_path);
    }

    public function test_branch_admin_cannot_see_another_branchs_offerings(): void
    {
        $admin = $this->branchAdmin();
        $otherOffering = CourseOffering::factory()->create();

        $response = $this->actingAs($admin)->get(route('offerings.index'));

        $response->assertOk();
        $response->assertDontSee($otherOffering->course->name);
    }

    public function test_central_admin_can_approve_a_pending_request(): void
    {
        $central = User::factory()->create();
        $central->assignRole('central_admin');
        $offering = CourseOffering::factory()->create(['status' => 'pending']);

        $this->actingAs($central)
            ->patch(route('offerings.approve', $offering))
            ->assertRedirect();

        $offering->refresh();
        $this->assertSame('approved', $offering->status->value);
        $this->assertSame($central->id, $offering->approved_by);
    }

    public function test_branch_admin_cannot_approve_a_request(): void
    {
        $admin = $this->branchAdmin();
        $offering = CourseOffering::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)->patch(route('offerings.approve', $offering))->assertForbidden();
    }

    public function test_cannot_re_decide_an_already_decided_request(): void
    {
        $central = User::factory()->create();
        $central->assignRole('central_admin');
        $offering = CourseOffering::factory()->create(['status' => 'approved']);

        $this->actingAs($central)->patch(route('offerings.approve', $offering))->assertStatus(422);
    }
}
