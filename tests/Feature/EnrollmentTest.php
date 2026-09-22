<?php

namespace Tests\Feature;

use App\Enums\BranchRequirement;
use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\User;
use App\Models\UserLicense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['system_admin', 'central_admin', 'branch_admin', 'user'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    public function test_user_can_self_register_for_an_open_course(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['branch_requirement' => BranchRequirement::None]);
        $offering = CourseOffering::factory()->create(['course_id' => $course->id, 'status' => 'approved']);

        $this->actingAs($user)
            ->post(route('enrollments.store', $offering))
            ->assertRedirect();

        $this->assertDatabaseHas('user_licenses', [
            'user_id' => $user->id,
            'course_offering_id' => $offering->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_self_register_for_a_course_restricted_to_another_branch(): void
    {
        $userBranch = Branch::factory()->create();
        $offeringBranch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $userBranch->id]);
        $course = Course::factory()->create(['branch_requirement' => BranchRequirement::RequiredOwn]);
        $offering = CourseOffering::factory()->create([
            'course_id' => $course->id, 'branch_id' => $offeringBranch->id, 'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->post(route('enrollments.store', $offering))
            ->assertStatus(422);
    }

    public function test_user_cannot_self_register_without_completing_the_prerequisite(): void
    {
        $user = User::factory()->create();
        $prerequisite = Course::factory()->create();
        $course = Course::factory()->create([
            'branch_requirement' => BranchRequirement::None,
            'prerequisite_course_id' => $prerequisite->id,
        ]);
        $offering = CourseOffering::factory()->create(['course_id' => $course->id, 'status' => 'approved']);

        $this->actingAs($user)
            ->post(route('enrollments.store', $offering))
            ->assertStatus(422);
    }

    public function test_branch_admin_can_approve_a_pending_self_registration(): void
    {
        $branch = Branch::factory()->create();
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $admin->assignRole('branch_admin');
        $offering = CourseOffering::factory()->create(['branch_id' => $branch->id, 'quota' => 5]);
        $license = UserLicense::factory()->create(['course_offering_id' => $offering->id, 'status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('licenses.approve', $license))
            ->assertRedirect();

        $license->refresh();
        $this->assertSame('active', $license->status->value);
        $this->assertSame($admin->id, $license->assigned_by);
    }

    public function test_approving_past_quota_keeps_the_request_pending(): void
    {
        $branch = Branch::factory()->create();
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $admin->assignRole('branch_admin');
        $offering = CourseOffering::factory()->create(['branch_id' => $branch->id, 'quota' => 1]);
        $offering->assignLicenseTo(User::factory()->create());
        $pending = UserLicense::factory()->create(['course_offering_id' => $offering->id, 'status' => 'pending']);

        $this->actingAs($admin)->patch(route('licenses.approve', $pending));

        $this->assertSame('pending', $pending->fresh()->status->value);
    }

    public function test_branch_admin_can_directly_assign_a_license_by_email(): void
    {
        $branch = Branch::factory()->create();
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $admin->assignRole('branch_admin');
        $applicant = User::factory()->create(['email' => 'applicant@example.com']);
        $offering = CourseOffering::factory()->create(['branch_id' => $branch->id]);

        $this->actingAs($admin)
            ->post(route('offerings.licenses.store', $offering), ['identifier' => 'applicant@example.com'])
            ->assertRedirect();

        $this->assertDatabaseHas('user_licenses', [
            'user_id' => $applicant->id,
            'course_offering_id' => $offering->id,
            'status' => 'active',
        ]);
    }

    public function test_branch_admin_can_import_a_csv_of_student_codes(): void
    {
        $branch = Branch::factory()->create();
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $admin->assignRole('branch_admin');
        $applicant = User::factory()->create(['student_code' => 'STU001']);
        $offering = CourseOffering::factory()->create(['branch_id' => $branch->id]);

        $csv = "student_code\nSTU001\nSTU999\n";
        $file = UploadedFile::fake()->createWithContent('applicants.csv', $csv);

        $this->actingAs($admin)
            ->post(route('offerings.licenses.import', $offering), ['file' => $file])
            ->assertRedirect();

        $this->assertDatabaseHas('user_licenses', [
            'user_id' => $applicant->id,
            'course_offering_id' => $offering->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseCount('user_licenses', 1);
    }
}
