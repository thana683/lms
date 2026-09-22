<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">คำขอเปิดหลักสูตร: {{ $offering->course->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2 text-sm">
                <p><span class="font-medium">สาขา:</span> {{ $offering->branch->name }}</p>
                <p><span class="font-medium">ผู้ขอ:</span> {{ $offering->requester->name }}</p>
                <p>
                    <span class="font-medium">ช่วงเวลา:</span>
                    {{ $offering->start_date->format('d/m/Y') }}
                    @if ($offering->end_date) - {{ $offering->end_date->format('d/m/Y') }} @endif
                </p>
                <p><span class="font-medium">โควตา:</span> {{ $offering->quota }}</p>
                <p><span class="font-medium">สถานะ:</span> {{ $offering->status->value }}</p>
                @if ($offering->approver)
                    <p><span class="font-medium">พิจารณาโดย:</span> {{ $offering->approver->name }} ({{ $offering->decided_at->format('d/m/Y H:i') }})</p>
                @endif
                <p>
                    <span class="font-medium">เอกสารแนบ:</span>
                    <a href="{{ route('offerings.attachment', $offering) }}" class="text-indigo-600 hover:underline">ดาวน์โหลด</a>
                </p>
                @if ($offering->status->value === 'approved' && (Auth::user()->hasRole('system_admin') || (Auth::user()->hasRole('branch_admin') && Auth::user()->branch_id === $offering->branch_id)))
                    <p>
                        <a href="{{ route('offerings.licenses.index', $offering) }}" class="text-indigo-600 hover:underline">จัดการสิทธิ์การเข้าเรียน →</a>
                    </p>
                @endif
            </div>

            @if ($offering->status->value === 'pending' && Auth::user()->hasAnyRole(['system_admin', 'central_admin']))
                <div class="bg-white shadow-sm sm:rounded-lg p-6 flex gap-2">
                    <form method="POST" action="{{ route('offerings.approve', $offering) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-md text-sm">อนุมัติ</button>
                    </form>
                    <form method="POST" action="{{ route('offerings.reject', $offering) }}"
                          onsubmit="return confirm('ยืนยันการปฏิเสธคำขอนี้?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-red-700 text-white rounded-md text-sm">ปฏิเสธ</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
