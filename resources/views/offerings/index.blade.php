<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">การอนุมัติเปิดหลักสูตร</h2>
            @if (Auth::user()->hasRole('branch_admin'))
                <a href="{{ route('offerings.create') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">+ ขอเปิดหลักสูตร</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">หลักสูตร</th>
                            <th class="p-4">สาขา</th>
                            <th class="p-4">ช่วงเวลา</th>
                            <th class="p-4">โควตา</th>
                            <th class="p-4">สถานะ</th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($offerings as $offering)
                            <tr>
                                <td class="p-4">{{ $offering->course->name }}</td>
                                <td class="p-4">{{ $offering->branch->name }}</td>
                                <td class="p-4">
                                    {{ $offering->start_date->format('d/m/Y') }}
                                    @if ($offering->end_date) - {{ $offering->end_date->format('d/m/Y') }} @endif
                                </td>
                                <td class="p-4">{{ $offering->quota }}</td>
                                <td class="p-4">
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs',
                                        'bg-yellow-100 text-yellow-800' => $offering->status->value === 'pending',
                                        'bg-green-100 text-green-800' => $offering->status->value === 'approved',
                                        'bg-red-100 text-red-800' => $offering->status->value === 'rejected',
                                    ])>{{ $offering->status->value }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('offerings.show', $offering) }}" class="text-indigo-600 hover:underline">ดูรายละเอียด</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">ยังไม่มีคำขอเปิดหลักสูตร</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $offerings->links() }}</div>
        </div>
    </div>
</x-app-layout>
