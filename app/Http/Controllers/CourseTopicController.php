<?php

namespace App\Http\Controllers;

use App\Enums\ContentMode;
use App\Models\CourseTerm;
use App\Models\CourseTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseTopicController extends Controller
{
    public function store(Request $request, CourseTerm $term): RedirectResponse
    {
        $data = $this->validated($request);

        $term->topics()->create($data);

        return back()->with('status', 'เพิ่มหัวข้อเรียบร้อยแล้ว');
    }

    public function update(Request $request, CourseTopic $topic): RedirectResponse
    {
        $topic->update($this->validated($request));

        return back()->with('status', 'บันทึกการแก้ไขหัวข้อเรียบร้อยแล้ว');
    }

    public function destroy(CourseTopic $topic): RedirectResponse
    {
        $topic->delete();

        return back()->with('status', 'ลบหัวข้อเรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $request->merge(['has_quiz' => $request->boolean('has_quiz')]);

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'day_number' => ['nullable', 'integer', 'min:1'],
            'content_type' => ['required', Rule::enum(ContentMode::class)],
            'content_url' => ['nullable', 'url', 'max:2048'],
            'has_quiz' => ['boolean'],
            'quiz_pass_score' => ['nullable', 'integer', 'min:0', 'max:100', 'required_if:has_quiz,1'],
            'order_number' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
