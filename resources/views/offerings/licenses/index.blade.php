<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            สิทธิ์การเข้าเรียน: {{ $offering->course->name }} ({{ $offering->branch->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md whitespace-pre-line">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-800 rounded-md">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">
                    โควตา {{ $offering->quota }} · เหลือ {{ $offering->remainingQuota() }}
                </p>

                <div class="flex flex-wrap gap-4">
                    <form method="POST" action="{{ route('offerings.licenses.store', $offering) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="identifier" placeholder="อีเมล หรือ รหัส นศ." required
                               class="rounded-md border-gray-300 text-sm">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">+ เพิ่มสิทธิ์การเข้าเรียน</button>
                    </form>

                    <form method="POST" action="{{ route('offerings.licenses.import', $offering) }}" enctype="multipart/form-data" class="flex gap-2">
                        @csrf
                        <input type="file" name="file" accept=".csv" required class="text-sm">
                        <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-md text-sm">นำเข้า CSV</button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">รายชื่อ</h3>
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-gray-500">
                        <tr>
                            <th class="pb-2">ผู้เรียน</th>
                            <th class="pb-2">อีเมล</th>
                            <th class="pb-2">สถานะ</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($offering->licenses as $license)
                            <tr>
                                <td class="py-2">{{ $license->user->name }}</td>
                                <td class="py-2">{{ $license->user->email }}</td>
                                <td class="py-2">{{ $license->status->value }}</td>
                                <td class="py-2 text-right space-x-2">
                                    @if ($license->status->value === 'pending')
                                        <form method="POST" action="{{ route('licenses.approve', $license) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs text-green-700 hover:underline">อนุมัติ</button>
                                        </form>
                                        <form method="POST" action="{{ route('licenses.reject', $license) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs text-red-700 hover:underline">ปฏิเสธ</button>
                                        </form>
                                    @elseif ($license->status->value === 'active')
                                        <form method="POST" action="{{ route('licenses.revoke', $license) }}"
                                              onsubmit="return confirm('ถอนสิทธิ์การเข้าเรียนของผู้เรียนนี้?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-700 hover:underline">ถอนสิทธิ์</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">ยังไม่มีผู้ลงทะเบียน</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
