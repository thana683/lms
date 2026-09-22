<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ระบบทะเบียน — ประวัติการเข้าอบรม</h2>
            <a href="{{ route('reports.enrollments.export') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Export CSV</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">ผู้เรียน</th>
                            <th class="p-4">สาขา</th>
                            <th class="p-4">หลักสูตร</th>
                            <th class="p-4">สถานะ</th>
                            <th class="p-4">วันที่ได้รับสิทธิ์</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($licenses as $license)
                            <tr>
                                <td class="p-4">{{ $license->user->name }}</td>
                                <td class="p-4">{{ $license->offering->branch->name }}</td>
                                <td class="p-4">{{ $license->offering->course->name }}</td>
                                <td class="p-4">{{ $license->status->value }}</td>
                                <td class="p-4">{{ optional($license->assigned_at)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">ยังไม่มีข้อมูล</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $licenses->links() }}</div>
        </div>
    </div>
</x-app-layout>
