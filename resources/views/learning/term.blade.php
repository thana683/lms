<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $term->course->name }} — {{ $term->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">
                    เรียนไปแล้ว {{ $completedCount }} / {{ $totalCount }} หัวข้อ
                    @if (! $eligibleForExam)
                        (เหลืออีก {{ $totalCount - $completedCount }} หัวข้อจึงจะมีสิทธิ์สอบ)
                    @endif
                </p>

                <table class="w-full text-left text-sm">
                    <thead class="border-b text-gray-500">
                        <tr>
                            <th class="pb-2">หัวข้อ</th>
                            <th class="pb-2">สถานะ</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($term->topics as $topic)
                            @php $p = $progress->get($topic->id); @endphp
                            <tr>
                                <td class="py-2">{{ $topic->title }}</td>
                                <td class="py-2">{{ $p && $p->completed_at ? '✓ เรียนจบแล้ว' : 'ยังไม่จบ' }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('topics.show', $topic) }}" class="text-indigo-600 hover:underline">เข้าเรียน</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($term->exams->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <a href="{{ route('exams.show', $term) }}" class="text-indigo-600 hover:underline text-sm">
                        ไปหน้าแบบทดสอบท้ายเทอม →
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
