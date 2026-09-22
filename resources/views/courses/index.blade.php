<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">การจัดการหลักสูตร</h2>
            <a href="{{ route('courses.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">+ สร้างหลักสูตร</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">ชื่อหลักสูตร</th>
                            <th class="p-4">รูปแบบการเปิด</th>
                            <th class="p-4">รูปแบบเนื้อหา</th>
                            <th class="p-4">จำนวนเทอม</th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($courses as $course)
                            <tr>
                                <td class="p-4">
                                    <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:underline">{{ $course->name }}</a>
                                </td>
                                <td class="p-4">{{ $course->schedule_type->value }}</td>
                                <td class="p-4">{{ $course->content_mode->value }}</td>
                                <td class="p-4">{{ $course->terms_count }}</td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('courses.edit', $course) }}" class="text-sm text-gray-600 hover:underline">แก้ไข</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">ยังไม่มีหลักสูตร</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $courses->links() }}</div>
        </div>
    </div>
</x-app-layout>
