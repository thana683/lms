<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">ลงทะเบียนเรียน</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-800 rounded-md">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">หลักสูตรที่เปิดรับสมัคร</h3>
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-gray-500">
                        <tr>
                            <th class="pb-2">หลักสูตร</th>
                            <th class="pb-2">สาขา</th>
                            <th class="pb-2">โควตาที่เหลือ</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($offerings as $offering)
                            <tr>
                                <td class="py-2">{{ $offering->course->name }}</td>
                                <td class="py-2">{{ $offering->branch->name }}</td>
                                <td class="py-2">{{ $offering->remainingQuota() }}</td>
                                <td class="py-2 text-right">
                                    @if ($offering->ineligibleReason)
                                        <span class="text-xs text-gray-400">{{ $offering->ineligibleReason }}</span>
                                    @else
                                        <form method="POST" action="{{ route('enrollments.store', $offering) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white rounded-md text-xs">สมัครเรียน</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">ยังไม่มีหลักสูตรที่เปิดรับสมัคร</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">การลงทะเบียนของฉัน</h3>
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-gray-500">
                        <tr>
                            <th class="pb-2">หลักสูตร</th>
                            <th class="pb-2">สถานะ</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($myLicenses as $license)
                            <tr>
                                <td class="py-2">{{ $license->offering->course->name }}</td>
                                <td class="py-2">{{ $license->status->value }}</td>
                                <td class="py-2 text-right">
                                    @if ($license->status->value === 'active' && $license->offering->course->terms->isNotEmpty())
                                        <a href="{{ route('learning.term', $license->offering->course->terms->first()) }}" class="text-indigo-600 hover:underline text-xs">เข้าเรียน →</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-500">ยังไม่มีการลงทะเบียน</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
