<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Subjects - Admin</title>
<style>body{font-family:system-ui;background:#f5f5f5;padding:40px;color:#333}
.card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 15px rgba(0,0,0,.1);margin:16px 0}
input,textarea,select{width:100%;padding:10px;margin:6px 0;border:1px solid #ddd;border-radius:6px;box-sizing:border-box}
button{padding:12px 24px;background:#667eea;color:#fff;border:none;border-radius:6px;font-weight:700;cursor:pointer}
table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
.alert{padding:12px;border-radius:8px;margin-bottom:16px}.alert-success{background:#d1e7dd;color:#0f5132}.alert-error{background:#f8d7da;color:#842029}
</style></head><body>
<h1>📚 Subjects</h1>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="card">
<h2>Create Subject</h2>
<form method="POST" action="{{ route('admin.subjects.store') }}">
@csrf
<input name="name" placeholder="Subject Name" required value="{{ old('name') }}">
<input name="slug" placeholder="slug (e.g. matematika)" required value="{{ old('slug') }}">
<textarea name="description" placeholder="Description" rows="2">{{ old('description') }}</textarea>
<button type="submit">+ Add Subject</button>
</form>
</div>

<div class="card">
<h2>All Subjects ({{ $subjects->count() }})</h2>
<table>
<tr><th>#</th><th>Name</th><th>Slug</th><th>Description</th><th>Created By</th><th>Actions</th></tr>
@forelse($subjects as $s)
<tr>
<td>{{ $s->id }}</td>
<td><strong>{{ $s->name }}</strong></td>
<td>{{ $s->slug }}</td>
<td>{{ Str::limit($s->description ?? '-', 40) }}</td>
<td>{{ $s->createdBy?->name ?? '-' }}</td>
<td>
<form method="POST" action="{{ route('admin.subjects.destroy', $s->id) }}" onsubmit="return confirm('Delete this subject?')" style="display:inline">
@csrf @method('DELETE')
<button style="background:#dc3545">Delete</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;color:#888;padding:20px">No subjects yet. Create your first subject above.</td></tr>
@endforelse
</table>
</div>
</body></html>