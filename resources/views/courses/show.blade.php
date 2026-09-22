<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $course->name }}</h2>
            <a href="{{ route('courses.edit', $course) }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">แก้ไขหลักสูตร</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2 text-sm">
                <p class="text-gray-600">{{ $course->description }}</p>
                <p><span class="font-medium">ช่วงเวลาที่เปิด:</span> {{ $course->schedule_type->value }}</p>
                <p><span class="font-medium">รูปแบบเนื้อหา:</span> {{ $course->content_mode->value }}</p>
                <p><span class="font-medium">เงื่อนไขสาขา:</span> {{ $course->branch_requirement->value }}</p>
                @if ($course->prerequisite)
                    <p><span class="font-medium">ต้องผ่านก่อน:</span> {{ $course->prerequisite->name }}</p>
                @endif
                @if ($course->pass_criteria)
                    <p><span class="font-medium">เกณฑ์การผ่าน:</span> {{ $course->pass_criteria }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">เทอมและหัวข้อการเรียน</h3>

                @foreach ($course->terms as $term)
                    <div class="border rounded-md p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <form method="POST" action="{{ route('course-terms.update', $term) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="name" value="{{ $term->name }}"
                                       class="font-medium border-0 border-b border-transparent hover:border-gray-300 focus:border-gray-400 focus:ring-0 p-0">
                                <button type="submit" class="text-xs text-gray-500 hover:underline">บันทึกชื่อ</button>
                            </form>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('learning.term', $term) }}" class="text-xs text-indigo-600 hover:underline">ดูหน้าเรียน</a>
                                <form method="POST" action="{{ route('course-terms.destroy', $term) }}"
                                      onsubmit="return confirm('ลบเทอมนี้และหัวข้อทั้งหมด?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">ลบเทอม</button>
                                </form>
                            </div>
                        </div>

                        <table class="w-full text-sm mb-3">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="pb-1">วันที่</th>
                                    <th class="pb-1">หัวข้อ</th>
                                    <th class="pb-1">รูปแบบเนื้อหา</th>
                                    <th class="pb-1">แบบทดสอบท้ายบท</th>
                                    <th class="pb-1"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse ($term->topics as $topic)
                                    <tr>
                                        <td class="py-2">{{ $topic->day_number }}</td>
                                        <td class="py-2">{{ $topic->title }}</td>
                                        <td class="py-2">{{ $topic->content_type->value }}</td>
                                        <td class="py-2">{{ $topic->has_quiz ? 'ผ่าน '.$topic->quiz_pass_score.'%' : '-' }}</td>
                                        <td class="py-2 text-right">
                                            <form method="POST" action="{{ route('course-topics.destroy', $topic) }}"
                                                  onsubmit="return confirm('ลบหัวข้อนี้?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:underline">ลบ</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-2 text-gray-400">ยังไม่มีหัวข้อ</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        <form method="POST" action="{{ route('course-topics.store', $term) }}" class="grid grid-cols-6 gap-2 items-end">
                            @csrf
                            <div class="col-span-1">
                                <label class="block text-xs text-gray-500">วันที่</label>
                                <input type="number" name="day_number" min="1" class="w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs text-gray-500">ชื่อหัวข้อ</label>
                                <input type="text" name="title" required class="w-full rounded-md border-gray-300 text-sm">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-xs text-gray-500">เนื้อหา</label>
                                <select name="content_type" class="w-full rounded-md border-gray-300 text-sm">
                                    @foreach (\App\Enums\ContentMode::cases() as $mode)
                                        <option value="{{ $mode->value }}">{{ $mode->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-1 flex items-center gap-1">
                                <input type="checkbox" name="has_quiz" value="1" id="has_quiz_{{ $term->id }}">
                                <label for="has_quiz_{{ $term->id }}" class="text-xs text-gray-500">มีแบบทดสอบ</label>
                                <input type="number" name="quiz_pass_score" min="0" max="100" placeholder="ผ่าน %"
                                       class="w-16 rounded-md border-gray-300 text-sm">
                            </div>
                            <div class="col-span-1">
                                <button type="submit" class="w-full px-3 py-2 bg-gray-700 text-white rounded-md text-xs">+ เพิ่มหัวข้อ</button>
                            </div>
                        </form>
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

                        <div class="mt-4 pt-4 border-t">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">แบบทดสอบท้ายเทอม</h4>
                            @forelse ($term->exams as $exam)
                                <p class="text-sm text-gray-600">
                                    {{ $exam->type->value }} · ผ่านที่ {{ $exam->pass_score }}%
                                    @if ($exam->max_attempts) · สอบได้ {{ $exam->max_attempts }} ครั้ง @endif
                                </p>
                            @empty
                                <form method="POST" action="{{ route('exams.store-for-term', $term) }}" class="grid grid-cols-5 gap-2 items-end">
                                    @csrf
                                    <div>
                                        <label class="block text-xs text-gray-500">รูปแบบ</label>
                                        <select name="type" class="w-full rounded-md border-gray-300 text-sm">
                                            @foreach (\App\Enums\ExamType::cases() as $type)
                                                <option value="{{ $type->value }}">{{ $type->value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500">จำนวนข้อ</label>
                                        <input type="number" name="question_count" min="1" class="w-full rounded-md border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500">เกณฑ์ผ่าน (%)</label>
                                        <input type="number" name="pass_score" min="0" max="100" required class="w-full rounded-md border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500">สอบได้กี่ครั้ง</label>
                                        <input type="number" name="max_attempts" min="1" class="w-full rounded-md border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <button type="submit" class="w-full px-3 py-2 bg-gray-700 text-white rounded-md text-xs">+ เพิ่มแบบทดสอบ</button>
                                    </div>
                                </form>
                            @endforelse
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('course-terms.store', $course) }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="ชื่อเทอมใหม่ เช่น เทอม 1" required
                           class="flex-1 rounded-md border-gray-300 text-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">+ เพิ่มเทอม</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
