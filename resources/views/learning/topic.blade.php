<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $topic->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-2">รูปแบบเนื้อหา: {{ $topic->content_type->value }}</p>
                @if ($topic->content_url)
                    <a href="{{ $topic->content_url }}" target="_blank" class="text-indigo-600 hover:underline text-sm">เปิดเนื้อหา</a>
                @endif
            </div>

            {{-- §1.4.1: presence check. ponytail: fixed 30s interval + manual confirm,
                 not the randomized silent-absence check the spec describes — add that
                 once there's a real player to hook the random sampling into. --}}
            <div id="presence-banner" class="bg-yellow-50 border border-yellow-200 rounded-md p-4 text-sm flex items-center justify-between">
                <span>ยืนยันว่าคุณยังอยู่หน้าจอ (ทุก 30 วินาที)</span>
                <button id="presence-confirm" class="px-3 py-1.5 bg-yellow-600 text-white rounded-md text-xs">ยืนยัน</button>
            </div>

            @if ($topic->has_quiz)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-2">แบบทดสอบท้ายบท (ผ่านที่ {{ $topic->quiz_pass_score }}%)</h3>
                    @if ($progress->quiz_passed)
                        <p class="text-green-700 text-sm">ผ่านแล้ว ({{ $progress->quiz_score }}%)</p>
                    @else
                        <form method="POST" action="{{ route('topics.quiz', $topic) }}" class="flex gap-2">
                            @csrf
                            <input type="number" name="quiz_score" min="0" max="100" required placeholder="คะแนนที่ได้ (%)"
                                   class="rounded-md border-gray-300 text-sm">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">ส่งคะแนน</button>
                        </form>
                    @endif
                </div>
            @endif

            @if ($topic->requires_practice_log)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-2">บันทึกผลปฏิบัติ</h3>
                    @if ($practiceSubmitted)
                        <p class="text-green-700 text-sm">บันทึกแล้ว</p>
                    @else
                        <form method="POST" action="{{ route('topics.practice-log', $topic) }}" class="space-y-3" id="practice-form">
                            @csrf
                            <div class="flex gap-4 items-center">
                                <label><input type="radio" name="activity" value="walking" required> เดินจงกรม</label>
                                <label><input type="radio" name="activity" value="sitting"> นั่งสมาธิ</label>
                            </div>

                            <div class="flex items-center gap-2">
                                <span id="timer-display" class="font-mono text-lg">00:00</span>
                                <button type="button" id="timer-start" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-xs">เริ่มจับเวลา</button>
                                <button type="button" id="timer-stop" class="px-3 py-1.5 bg-gray-500 text-white rounded-md text-xs" disabled>หยุด</button>
                                <input type="hidden" name="duration_seconds" id="duration_seconds" value="0" required>
                            </div>

                            <textarea name="notes" rows="2" placeholder="บันทึกเพิ่มเติม" class="w-full rounded-md border-gray-300 text-sm"></textarea>

                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">บันทึกผลปฏิบัติ</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const heartbeatUrl = @json(route('topics.heartbeat', $topic));

            setInterval(() => {
                document.getElementById('presence-banner')?.classList.remove('hidden');
            }, 30000);

            document.getElementById('presence-confirm')?.addEventListener('click', () => {
                fetch(heartbeatUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                });
                document.getElementById('presence-banner')?.classList.add('hidden');
            });

            let seconds = 0, interval = null;
            const display = document.getElementById('timer-display');
            const startBtn = document.getElementById('timer-start');
            const stopBtn = document.getElementById('timer-stop');
            const input = document.getElementById('duration_seconds');

            startBtn?.addEventListener('click', () => {
                startBtn.disabled = true;
                stopBtn.disabled = false;
                interval = setInterval(() => {
                    seconds++;
                    const m = String(Math.floor(seconds / 60)).padStart(2, '0');
                    const s = String(seconds % 60).padStart(2, '0');
                    display.textContent = `${m}:${s}`;
                    input.value = seconds;
                }, 1000);
            });

            stopBtn?.addEventListener('click', () => {
                clearInterval(interval);
                stopBtn.disabled = true;
            });
        })();
    </script>
</x-app-layout>
