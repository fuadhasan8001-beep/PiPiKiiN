<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Game Session - Adventure Edu</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:system-ui;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;min-height:100vh;margin:0;padding:30px}
.card{background:#fff;color:#333;border-radius:16px;padding:30px;margin:16px 0;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.timer{font-size:64px;font-weight:900;text-align:center;color:#764ba2}
.timer-bar{height:10px;background:#eee;border-radius:5px;overflow:hidden;margin:16px 0}
.timer-fill{height:100%;background:linear-gradient(90deg,#4ecdc4,#667eea);transition:width 1s linear}
.opt{display:block;width:100%;text-align:left;padding:16px;margin:10px 0;border:2px solid #ddd;border-radius:12px;background:#fff;cursor:pointer;font-size:16px;font-weight:600;transition:.2s}
.opt:hover{border-color:#667eea;background:#f4f4ff}
.opt.selected{border-color:#667eea;background:#e8e8ff}
.leaderboard{width:100%;border-collapse:collapse}.leaderboard td,.leaderboard th{padding:10px;border-bottom:1px solid #eee}
.rank-1{background:linear-gradient(90deg,#fff3cd,#fff);font-weight:900}
</style></head><body>
<h1>🎯 {{ $session->room->quiz->title }}</h1>

<div class="card">
<div style="text-align:center;color:#888">Waktu tersisa</div>
<div class="timer" id="timer">--</div>
<div class="timer-bar"><div class="timer-fill" id="fill" style="width:100%"></div></div>
<p style="text-align:center;color:#666;margin:0">Pertanyaan <span id="idx">{{ $session->current_question_index + 1 }}</span> dari {{ $session->total_questions }}</p>
</div>

<div class="card">
<h2 id="qtext">Memuat pertanyaan...</h2>
<form method="POST" action="{{ route('game.answer', $session->id) }}">
@csrf
<input type="hidden" name="question_id" id="qid">
<input type="hidden" name="response_ms" id="rms" value="0">
<textarea name="answer_text" id="atext" rows="2" placeholder="Ketik jawabanmu..." style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin:12px 0"></textarea>
<button class="opt" style="text-align:center;background:#667eea;color:#fff;border-color:#667eea">Kirim Jawaban</button>
</form>
</div>

<div class="card">
<h3>🏆 Leaderboard</h3>
<table class="leaderboard">
<tr><th>#</th><th>Nama</th><th>Skor</th></tr>
@foreach($session->room->participants->sortByDesc('score') as $p)
<tr class="{{ $loop->iteration === 1 ? 'rank-1' : '' }}">
<td>{{ $loop->iteration }}</td><td>{{ $p->display_name }}</td><td>{{ $p->score }}</td>
</tr>
@endforeach
</table>
</div>

<script>
const dur = {{ $session->total_questions > 0 ? 30 : 30 }};
let remaining = dur;
const qid = document.getElementById('qid');
qid.value = '{{ $session->room->quiz->questions->first()?->id ?? "" }}';
document.getElementById('qtext').textContent = '{{ $session->room->quiz->questions->first()?->text ?? "Belum ada pertanyaan" }}';
const started = Date.now();
setInterval(() => {
    remaining = Math.max(0, dur - Math.floor((Date.now() - started) / 1000));
    document.getElementById('timer').textContent = remaining + 's';
    document.getElementById('fill').style.width = (remaining / dur * 100) + '%';
    document.getElementById('rms').value = (Date.now() - started);
}, 1000);
</script>
</body></html>