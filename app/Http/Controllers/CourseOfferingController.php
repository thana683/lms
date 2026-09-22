<?php

namespace App\Http\Controllers;

use App\Enums\OfferingStatus;
use App\Models\Course;
use App\Models\CourseOffering;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseOfferingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CourseOffering::class);

        $user = $request->user();

        $offerings = CourseOffering::with(['course', 'branch', 'requester'])
            ->when(
                $user->hasRole('branch_admin') && ! $user->hasAnyRole(['system_admin', 'central_admin']),
                fn ($query) => $query->where('branch_id', $user->branch_id)
            )
            ->latest()
            ->paginate(15);

        return view('offerings.index', compact('offerings'));
    }

    public function create(): View
    {
        $this->authorize('create', CourseOffering::class);

        $courses = Course::orderBy('name')->get();

        return view('offerings.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CourseOffering::class);

        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'quota' => ['required', 'integer', 'min:1'],
            'attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $data['attachment_path'] = $request->file('attachment')->store('offering-attachments', 'local');
        unset($data['attachment']);

        $offering = Auth::user()->branch->offerings()->create([
            ...$data,
            'requested_by' => Auth::id(),
            'status' => OfferingStatus::Pending,
        ]);

        return redirect()->route('offerings.show', $offering)->with('status', 'ส่งคำขอเปิดหลักสูตรเรียบร้อยแล้ว');
    }

    public function show(CourseOffering $offering): View
    {
        $this->authorize('view', $offering);

        $offering->load(['course', 'branch', 'requester', 'approver']);

        return view('offerings.show', compact('offering'));
    }

    public function downloadAttachment(CourseOffering $offering): HttpResponse
    {
        $this->authorize('view', $offering);

        return Storage::disk('local')->download($offering->attachment_path);
    }

    public function approve(CourseOffering $offering): RedirectResponse
    {
        $this->authorize('decide', $offering);

        abort_unless($offering->status === OfferingStatus::Pending, 422, 'คำขอนี้ถูกพิจารณาไปแล้ว');

        $offering->update([
            'status' => OfferingStatus::Approved,
            'approved_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        return back()->with('status', 'อนุมัติเปิดหลักสูตรเรียบร้อยแล้ว');
    }

    public function reject(CourseOffering $offering): RedirectResponse
    {
        $this->authorize('decide', $offering);

        abort_unless($offering->status === OfferingStatus::Pending, 422, 'คำขอนี้ถูกพิจารณาไปแล้ว');

        $offering->update([
            'status' => OfferingStatus::Rejected,
            'approved_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        return back()->with('status', 'ปฏิเสธคำขอเปิดหลักสูตรแล้ว');
    }
}
