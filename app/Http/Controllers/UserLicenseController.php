<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\User;
use App\Models\UserLicense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class UserLicenseController extends Controller
{
    public function index(CourseOffering $offering): View
    {
        $this->authorize('manageEnrollment', $offering);

        $offering->load(['licenses.user']);

        return view('offerings.licenses.index', compact('offering'));
    }

    /**
     * §1.3.2: branch admin assigns a license directly to a known user.
     */
    public function store(Request $request, CourseOffering $offering): RedirectResponse
    {
        $this->authorize('manageEnrollment', $offering);

        $data = $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['identifier'])
            ->orWhere('student_code', $data['identifier'])
            ->first();

        if (! $user) {
            return back()->withErrors(['identifier' => 'ไม่พบผู้ใช้นี้ในระบบ (ต้องมี User Samathi101 ก่อน)']);
        }

        try {
            $offering->assignLicenseTo($user, Auth::user());
        } catch (RuntimeException $e) {
            return back()->withErrors(['identifier' => $e->getMessage()]);
        }

        return back()->with('status', "เพิ่มสิทธิ์การเข้าเรียนให้ {$user->name} เรียบร้อยแล้ว");
    }

    /**
     * §1.3.3: bulk-assign from a CSV export of the branch's applicant list.
     * ponytail: CSV via stdlib fgetcsv, not a real .xlsx parser — swap in
     * maatwebsite/excel if branches insist on uploading raw .xlsx files.
     */
    public function import(Request $request, CourseOffering $offering): RedirectResponse
    {
        $this->authorize('manageEnrollment', $offering);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = array_map('trim', fgetcsv($handle) ?: []);
        $identifierColumn = array_search('student_code', $header, true);
        if ($identifierColumn === false) {
            $identifierColumn = array_search('email', $header, true);
        }

        abort_if($identifierColumn === false, 422, 'ไฟล์ต้องมีคอลัมน์ student_code หรือ email');

        $imported = 0;
        $skipped = [];

        while (($row = fgetcsv($handle)) !== false) {
            $identifier = trim($row[$identifierColumn] ?? '');
            if ($identifier === '') {
                continue;
            }

            $user = User::where('email', $identifier)->orWhere('student_code', $identifier)->first();

            if (! $user) {
                $skipped[] = "{$identifier} (ไม่พบผู้ใช้)";

                continue;
            }

            try {
                $offering->assignLicenseTo($user, Auth::user());
                $imported++;
            } catch (RuntimeException $e) {
                $skipped[] = "{$identifier} ({$e->getMessage()})";
            }
        }

        fclose($handle);

        $status = "นำเข้าสำเร็จ {$imported} รายการ";
        if ($skipped) {
            $status .= ' | ข้าม: '.implode(', ', $skipped);
        }

        return back()->with('status', $status);
    }

    public function approve(UserLicense $license): RedirectResponse
    {
        $this->authorize('manageEnrollment', $license->offering);

        try {
            $license->approve(Auth::user());
        } catch (RuntimeException $e) {
            return back()->withErrors(['license' => $e->getMessage()]);
        }

        return back()->with('status', 'อนุมัติสิทธิ์การเข้าเรียนเรียบร้อยแล้ว');
    }

    public function reject(UserLicense $license): RedirectResponse
    {
        $this->authorize('manageEnrollment', $license->offering);

        try {
            $license->reject();
        } catch (RuntimeException $e) {
            return back()->withErrors(['license' => $e->getMessage()]);
        }

        return back()->with('status', 'ปฏิเสธคำขอเรียบร้อยแล้ว');
    }

    public function revoke(UserLicense $license): RedirectResponse
    {
        $this->authorize('manageEnrollment', $license->offering);

        $license->revoke();

        return back()->with('status', 'ถอนสิทธิ์การเข้าเรียนเรียบร้อยแล้ว');
    }
}
