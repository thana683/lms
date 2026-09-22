<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">ขอเปิดหลักสูตร</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('offerings.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">ชื่อหลักสูตรที่ขอเปิด</label>
                        <select name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">— เลือกหลักสูตร —</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->name }}</option>
                            @endforeach
                        </select>
                        @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">วันที่เริ่มต้น</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">วันที่สิ้นสุด (ไม่บังคับ)</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">จำนวนผู้เข้าอบรมสูงสุด (User License Quota)</label>
                        <input type="number" name="quota" min="1" value="{{ old('quota') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @error('quota') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            ไฟล์สำเนาหนังสือขออนุมัติเปิดหลักสูตร (ลงนามแล้ว)
                        </label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                               class="mt-1 block w-full text-sm" required>
                        @error('attachment') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('offerings.index') }}" class="px-4 py-2 text-sm text-gray-600">ยกเลิก</a>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">ส่งคำขอ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
