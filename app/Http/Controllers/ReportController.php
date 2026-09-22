<?php

namespace App\Http\Controllers;

use App\Models\UserLicense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * §1.6.1/§1.6.2: Admin สาขา sees their own branch's history, Admin ส่วนกลาง/System Admin see everyone's.
     */
    public function index(Request $request): View
    {
        $licenses = $this->scopedQuery($request)->paginate(20)->withQueryString();

        return view('reports.enrollments', compact('licenses'));
    }

    /**
     * §1.6.3: export the same (scoped) list as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $licenses = $this->scopedQuery($request)->get();

        return response()->streamDownload(function () use ($licenses) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ผู้เรียน', 'อีเมล', 'สาขา', 'หลักสูตร', 'สถานะ', 'วันที่ได้รับสิทธิ์', 'วันที่ถอนสิทธิ์']);

            foreach ($licenses as $license) {
                fputcsv($out, [
                    $license->user->name,
                    $license->user->email,
                    $license->offering->branch->name,
                    $license->offering->course->name,
                    $license->status->value,
                    optional($license->assigned_at)->format('Y-m-d H:i'),
                    optional($license->revoked_at)->format('Y-m-d H:i'),
                ]);
            }

            fclose($out);
        }, 'enrollment-report-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function scopedQuery(Request $request): Builder
    {
        $user = $request->user();

        return UserLicense::with(['user', 'offering.course', 'offering.branch'])
            ->when(
                $user->hasRole('branch_admin') && ! $user->hasAnyRole(['system_admin', 'central_admin']),
                fn ($query) => $query->whereHas('offering', fn ($q) => $q->where('branch_id', $user->branch_id))
            )
            ->latest();
    }
}
