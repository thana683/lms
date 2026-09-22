<?php

namespace App\Policies;

use App\Models\CourseOffering;
use App\Models\User;

class CourseOfferingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['system_admin', 'central_admin', 'branch_admin']);
    }

    public function view(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasAnyRole(['system_admin', 'central_admin'])
            || ($user->hasRole('branch_admin') && $user->branch_id === $courseOffering->branch_id);
    }

    /**
     * §1.2.1: only a branch admin, for their own branch, requests to open a course.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('branch_admin') && $user->branch_id !== null;
    }

    /**
     * §1.2.2: only central (or system) admin decides on a pending request.
     */
    public function decide(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasAnyRole(['system_admin', 'central_admin']);
    }

    /**
     * §1.3.2/§1.3.3: assigning/approving/importing licenses is a branch-admin job,
     * scoped to their own branch's offerings. System admin can act on any branch.
     */
    public function manageEnrollment(User $user, CourseOffering $courseOffering): bool
    {
        return $user->hasRole('system_admin')
            || ($user->hasRole('branch_admin') && $user->branch_id === $courseOffering->branch_id);
    }
}
