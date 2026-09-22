<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">แก้ไขหลักสูตร: {{ $course->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('courses.update', $course) }}">
                    @csrf
                    @method('PATCH')
                    @include('courses._form')

                    <div class="mt-6 flex justify-end gap-2">
                        <a href="{{ route('courses.show', $course) }}" class="px-4 py-2 text-sm text-gray-600">ยกเลิก</a>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">บันทึก</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('courses.destroy', $course) }}"
                      onsubmit="return confirm('ยืนยันการลบหลักสูตรนี้?');" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm text-red-600">ลบหลักสูตร</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
