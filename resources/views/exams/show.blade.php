<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">แบบทดสอบท้ายเทอม: {{ $term->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-800 rounded-md">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-sm space-y-1">
                <p><span class="font-medium">รูปแบบ:</span> {{ $exam->type->value }}</p>
                <p><span class="font-medium">เกณฑ์คะแนนที่ผ่าน:</span> {{ $exam->pass_score }}%</p>
                @if ($exam->question_count)
                    <p><span class="font-medium">จำนวนข้อสอบ:</span> {{ $exam->question_count }}</p>
                @endif
                @if ($exam->max_attempts)
                    <p><span class="font-medium">จำนวนครั้งที่สอบได้:</span> {{ $exam->max_attempts }}</p>
                @endif
            </div>

            @if ($blockReason)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-600">{{ $blockReason }}</div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    @if ($exam->type->value === 'objective')
                        <form method="POST" action="{{ route('exam-attempts.store', $exam) }}" class="flex gap-2">
                            @csrf
                            <input type="number" name="score" min="0" max="100" required placeholder="คะแนนที่ได้ (%)"
                                   class="rounded-md border-gray-300 text-sm">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">ส่งคำตอบ</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('exam-attempts.store', $exam) }}" enctype="multipart/form-data" class="flex gap-2">
                            @csrf
                            <input type="file" name="answer_file" required class="text-sm">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">ส่งกระดาษคำตอบ</button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">ประวัติการสอบ</h3>
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-gray-500">
                        <tr>
                            <th class="pb-2">ครั้งที่</th>
                            <th class="pb-2">คะแนน</th>
                            <th class="pb-2">ผล</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($attempts as $attempt)
                            <tr>
                                <td class="py-2">{{ $attempt->attempt_number }}</td>
                                <td class="py-2">{{ $attempt->score ?? '-' }}</td>
                                <td class="py-2">
                                    @if (is_null($attempt->passed)) รอตรวจ
                                    @elseif ($attempt->passed) ผ่าน
                                    @else ไม่ผ่าน
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-500">ยังไม่มีการสอบ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
