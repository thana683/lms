<?php

namespace App\Http\Controllers;

use App\Enums\LicenseStatus;
use App\Enums\OfferingStatus;
use App\Models\CourseOffering;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * §1.3.1: self-service registration — browse open offerings and see
     * eligibility for each, plus the user's own requests/licenses.
     */
    public function index(): View
    {
        $user = Auth::user();

        $offerings = CourseOffering::with('course', 'branch')
            ->where('status', OfferingStatus::Approved)
            ->get()
            ->map(fn (CourseOffering $offering) => tap($offering, fn ($o) => $o->ineligibleReason = $offering->eligibilityErrorFor($user)));

        $myLicenses = $user->licenses()->with('offering.course.terms')->latest()->get();

        return view('enrollments.index', compact('offerings', 'myLicenses'));
    }

    public function store(CourseOffering $offering): RedirectResponse
    {
        $error = $offering->eligibilityErrorFor(Auth::user());

        abort_if($error, 422, $error);

        $offering->licenses()->create([
            'user_id' => Auth::id(),
            'status' => LicenseStatus::Pending,
        ]);

        return back()->with('status', 'ส่งคำขอลงทะเบียนเรียบร้อยแล้ว รอ Admin สาขาอนุมัติ');
    }
}
