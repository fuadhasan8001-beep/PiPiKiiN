<div class="adventure-page">
    <style>
        .adventure-page{font-family:system-ui;background:linear-gradient(135deg,#1a1a2e,#16213e);min-height:100vh;padding:30px;color:#fff}
        .adv-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:28px;margin:16px 0}
        .timer{font-size:48px;font-weight:900;text-align:center;color:#4ecdc4}
        .timer-bar{height:10px;background:#fff3;border-radius:5px;overflow:hidden;margin:12px 0}
        .timer-fill{height:100%;background:#4ecdc4;transition:width 1s linear}
        .adv-opt{display:block;width:100%;text-align:left;padding:14px 16px;margin:8px 0;border:2px solid rgba(255,255,255,.15);border-radius:12px;background:rgba(255,255,255,.05);cursor:pointer;font-size:15px;color:#fff;transition:.2s}
        .adv-opt:hover{border-color:#4ecdc4;background:rgba(78,205,196,.08)}
        .adv-btn{padding:12px 28px;border:none;border-radius:10px;font-weight:700;background:#4ecdc4;color:#000;cursor:pointer}
        .adv-btn:disabled{opacity:.5;cursor:not-allowed}
        .adv-hero{font-size:48px;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
        .result-correct{color:#4ecdc4;font-weight:700}.result-wrong{color:#ff6b6b;font-weight:700}
        .score-summary{font-size:24px;text-align:center;font-weight:700;margin-top:16px}
    </style>

    @if ($finished)
        <div class="adv-card" style="text-align:center">
            <h1 style="font-size:48px;margin-bottom:8px">🎉 Selesai!</h1>
            <p style="color:#aaa;font-size:16px">Kuis: <strong>{{ $quiz->title }}</strong></p>
            <div class="score-summary">
                <span style="color:#4ecdc4">{{ $attempt?->correct_count ?? 0 }}</span> / <span style="color:#fff">{{ count($questions) }}</span>
                <span style="font-size:14px;color:#aaa">benar</span>
                <span style="margin-left:8px;color:#fff;font-size:14px">· Skor: {{ $attempt?->score ?? 0 }}</span>
            </div>
            <a href="{{ route('dashboard') }}" class="adv-btn" style="display:inline-block;margin-top:16px">Kembali ke Dashboard</a>
            <a href="{{ route('solo.start', $quiz->id) }}" class="adv-btn" style="display:inline-block;margin-top:16px;background:#667eea;color:#fff">Ulang Solo Practice</a>
        </div>
    @else
        <h1 class="adv-hero">{{ $quiz->title }}</h1>
        <p style="text-align:center;color:#aaa;margin-top:-8px">Solo Practice · Pertanyaan {{ $index + 1 }}/{{ $total }}</p>

        <div class="adv-card" style="text-align:center">
            <div style="font-size:48px;font-weight:900;color:#4ecdc4" id="timer">{{ $question['time_limit'] ?? 30 }}</div>
            <div class="timer-bar"><div class="timer-fill" id="fill" style="width:100%"></div></div>
        </div>

        @if ($question)
            <div class="adv-card">
                <h2>{{ $question['text'] }}</h2>
                @if ($question['image_url'])
                    <img src="{{ $question['image_url'] }}" alt="Soal" style="max-width:100%;border-radius:8px;margin:12px 0">
                @endif

                @if ($lastPoints !== null)
                    <div style="padding:14px;border-radius:10px;margin-bottom:16px;{{ $lastCorrect ? 'background:rgba(78,205,196,.15);color:#4ecdc4' : 'background:rgba(255,107,107,.15);color:#ff6b6b' }}">
                        <strong>{{ $lastCorrect ? '✅ Benar!' : '❌ Salah' }}</strong> — Poin: <strong>{{ $lastPoints }}</strong>
                        @if ($lastExplanation)
                            <p style="margin:4px 0 0;font-size:14px;color:#ddd">{{ $lastExplanation }}</p>
                        @endif
                    </div>
                @endif

                <form wire:submit.prevent="submit">
                    @if (!empty($question['options']))
                        @foreach ($question['options'] as $label => $val)
                            <label class="adv-opt" style="display:flex;align-items:center;gap:10px;cursor:pointer">
                                <input type="radio" wire:model="answer" value="{{ $label }}" style="width:20px;height:20px;cursor:pointer">
                                <span>{{ $label }} — {{ $val }}</span>
                            </label>
                        @endforeach
                    @else
                        <textarea wire:model="answer" rows="3" placeholder="Ketik jawabanmu..." class="adv-opt" style="resize:vertical;background:#fff;color:#333;border:2px solid #ddd"></textarea>
                    @endif
                    <div style="margin-top:12px">
                        <button type="submit" class="adv-btn" wire:loading.attr="disabled" wire:target="submit">
                            <span wire:loading.remove wire:target="submit">Kirim Jawaban</span>
                            <span wire:loading wire:target="submit">Mengirim...</span>
                        </button>
                        @if ($lastPoints !== null)
                            <button type="button" wire:click="next" class="adv-btn" style="margin-left:8px;background:#667eea;color:#fff">Lanjut →</button>
                        @endif
                    </div>
                </form>
            </div>
            <p style="text-align:center;color:#aaa;font-size:13px;margin-top:8px">Jawaban divalidasi oleh server — tidak bisa diubah setelah dikirim.</p>
        @endif
    @endif
</div>

<script>
(function() {
    const startMs = Date.now();
    const dur = {{ $question['time_limit'] ?? 30 }};
    setInterval(() => {
        const rem = Math.max(0, dur - Math.floor((Date.now() - startMs) / 1000));
        const timerEl = document.getElementById('timer');
        const fillEl = document.getElementById('fill');
        if (timerEl) timerEl.textContent = rem + 's';
        if (fillEl) fillEl.style.width = (rem / dur * 100) + '%';
    }, 1000);
})();
</script>
