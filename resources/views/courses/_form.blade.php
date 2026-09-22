@php
    $course = $course ?? null;
@endphp

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">ชื่อหลักสูตร</label>
        <input type="text" name="name" value="{{ old('name', $course?->name) }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">คำอธิบายหลักสูตร</label>
        <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $course?->description) }}</textarea>
        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">ช่วงเวลาที่เปิดหลักสูตร</label>
            <select name="schedule_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @foreach (\App\Enums\ScheduleType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('schedule_type', $course?->schedule_type?->value) === $type->value)>
                        {{ match ($type) {
                            \App\Enums\ScheduleType::Once => 'รายครั้ง (มีวันเริ่มต้น-สิ้นสุด)',
                            \App\Enums\ScheduleType::Weekly => 'ทุกสัปดาห์',
                            \App\Enums\ScheduleType::Monthly => 'ทุกเดือน',
                            \App\Enums\ScheduleType::Yearly => 'ทุกปี',
                        } }}
                    </option>
                @endforeach
            </select>
            @error('schedule_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">รูปแบบเนื้อหา</label>
            <select name="content_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @foreach (\App\Enums\ContentMode::cases() as $mode)
                    <option value="{{ $mode->value }}" @selected(old('content_mode', $course?->content_mode?->value) === $mode->value)>
                        {{ match ($mode) {
                            \App\Enums\ContentMode::Video => 'คลิป VDO',
                            \App\Enums\ContentMode::Live => 'Live Streaming',
                            \App\Enums\ContentMode::Mixed => 'ผสมผสาน',
                        } }}
                    </option>
                @endforeach
            </select>
            @error('content_mode') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            กำหนดวันที่เรียนได้ / รอบการเปิด (JSON, ไม่บังคับ)
        </label>
        <textarea name="schedule_rule" rows="2" placeholder='{"days": ["sat", "sun"]}'
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono text-sm">{{ old('schedule_rule', $course?->schedule_rule ? json_encode($course->schedule_rule) : '') }}</textarea>
        @error('schedule_rule') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">เงื่อนไขสาขาในการลงทะเบียน</label>
        <select name="branch_requirement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            @foreach (\App\Enums\BranchRequirement::cases() as $req)
                <option value="{{ $req->value }}" @selected(old('branch_requirement', $course?->branch_requirement?->value) === $req->value)>
                    {{ match ($req) {
                        \App\Enums\BranchRequirement::RequiredOwn => 'ต้องมีสาขาที่สังกัด และสมัครได้เฉพาะสาขาที่สังกัด',
                        \App\Enums\BranchRequirement::RequiredAny => 'ต้องมีสาขาที่สังกัด แต่สมัครที่สาขาอื่นได้',
                        \App\Enums\BranchRequirement::None => 'ไม่ต้องมีสาขาที่สังกัด',
                    } }}
                </option>
            @endforeach
        </select>
        @error('branch_requirement') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">หลักสูตรที่ต้องผ่านก่อน (ไม่บังคับ)</label>
        <select name="prerequisite_course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">— ไม่มี —</option>
            @foreach ($courses as $option)
                <option value="{{ $option->id }}" @selected((string) old('prerequisite_course_id', $course?->prerequisite_course_id) === (string) $option->id)>
                    {{ $option->name }}
                </option>
            @endforeach
        </select>
        @error('prerequisite_course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">เกณฑ์การผ่านหลักสูตร</label>
        <textarea name="pass_criteria" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('pass_criteria', $course?->pass_criteria) }}</textarea>
        @error('pass_criteria') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
