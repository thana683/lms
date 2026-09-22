<?php

namespace App\Http\Controllers;

use App\Enums\BranchRequirement;
use App\Enums\ContentMode;
use App\Enums\ScheduleType;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::withCount('terms')->latest()->paginate(15);

        return view('courses.index', compact('courses'));
    }

    public function create(): View
    {
        $courses = Course::orderBy('name')->get();

        return view('courses.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = Auth::id();

        $course = Course::create($data);

        return redirect()->route('courses.show', $course)->with('status', 'สร้างหลักสูตรเรียบร้อยแล้ว');
    }

    public function show(Course $course): View
    {
        $course->load('terms.topics', 'terms.exams', 'prerequisite');

        return view('courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $courses = Course::where('id', '!=', $course->id)->orderBy('name')->get();

        return view('courses.edit', compact('course', 'courses'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $course->update($this->validated($request, $course));

        return redirect()->route('courses.show', $course)->with('status', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('courses.index')->with('status', 'ลบหลักสูตรเรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Course $course = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'schedule_type' => ['required', Rule::enum(ScheduleType::class)],
            'schedule_rule' => ['nullable', 'json'],
            'content_mode' => ['required', Rule::enum(ContentMode::class)],
            'branch_requirement' => ['required', Rule::enum(BranchRequirement::class)],
            'prerequisite_course_id' => [
                'nullable',
                Rule::exists('courses', 'id')->when($course, fn ($rule) => $rule->whereNot('id', $course->id)),
            ],
            'pass_criteria' => ['nullable', 'string'],
        ]);

        if (! empty($data['schedule_rule'])) {
            $data['schedule_rule'] = json_decode($data['schedule_rule'], true);
        }

        return $data;
    }
}
