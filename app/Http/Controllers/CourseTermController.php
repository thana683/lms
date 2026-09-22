<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseTermController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'order_number' => ['nullable', 'integer', 'min:0'],
        ]);

        $course->terms()->create($data);

        return back()->with('status', 'เพิ่มเทอมเรียบร้อยแล้ว');
    }

    public function update(Request $request, CourseTerm $term): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'order_number' => ['nullable', 'integer', 'min:0'],
        ]);

        $term->update($data);

        return back()->with('status', 'บันทึกการแก้ไขเทอมเรียบร้อยแล้ว');
    }

    public function destroy(CourseTerm $term): RedirectResponse
    {
        $term->delete();

        return back()->with('status', 'ลบเทอมเรียบร้อยแล้ว');
    }
}
