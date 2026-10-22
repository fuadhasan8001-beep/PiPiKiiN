<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Room {{ $room->code }} - Adventure Edu</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:system-ui;background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;min-height:100vh;margin:0;padding:30px}
.card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:24px;margin:16px 0}
.code{font-size:56px;font-weight:900;letter-spacing:8px;color:#4ecdc4;text-align:center;background:rgba(78,205,196,.1);padding:20px;border-radius:12px;letter-spacing:10px}
table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,.1);text-align:left}
.btn{padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;display:inline-block;margin:4px;background:#4ecdc4;color:#000;border:none;cursor:pointer}
.btn-ghost{background:transparent;color:#4ecdc4;border:2px solid #4ecdc4}
.alert{padding:12px;border-radius:8px;margin-bottom:12px}.alert-success{background:rgba(78,205,196,.2);color:#4ecdc4}.alert-error{background:rgba(255,107,107,.2);color:#ff6b6b}
.badge{background:#667eea;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700}
.player{display:flex;align-items:center;gap:10px;padding:8px 0}
.avatar{width:36px;height:36px;border-radius:50%;background:#667eea;display:flex;align-items:center;justify-content:center;font-weight:700}
</style></head><body>
<h1>🏠 Room Lobby</h1>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

<div class="card">
<p style="text-align:center;color:#aaa;margin:0">Kode Room</p>
<div class="code">{{ $room->code }}</div>
<p style="text-align:center;color:#aaa">Kuis: <strong>{{ $room->quiz->title }}</strong> · Host: {{ $room->host->name }}</p>
</div>

<div class="card">
<h2>👥 Peserta ({{ $room->participants->count() }}/{{ $room->max_players }})</h2>
<table>
<tr><th>#</th><th>Nama</th><th>Status</th><th>Skor</th></tr>
@forelse($room->participants as $p)
<tr>
<td>{{ $loop->iteration }}</td>
<td><div class="player"><div class="avatar">{{ strtoupper(substr($p->display_name,0,1)) }}</div>{{ $p->display_name }}</div></td>
<td><span class="badge">{{ $p->status }}</span></td>
<td>{{ $p->score }}</td>
</tr>
@empty
<tr><td colspan="4" style="color:#888;text-align:center;padding:16px">Belum ada peserta. Bagikan kode room!</td></tr>
@endforelse
</table>
</div>

<div class="card" style="text-align:center">
@if($room->host_id === auth()->id())
<a href="{{ route('room.start', $room->code) }}" class="btn">▶ Mulai Game</a>
@else
<p style="color:#aaa">Menunggu host memulai permainan...</p>
@endif
<a href="{{ route('dashboard') }}" class="btn btn-ghost">Kembali</a>
</div>
</body></html>