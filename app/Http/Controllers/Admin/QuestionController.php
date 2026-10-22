<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Quiz;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionController extends Controller
{
    public function __construct(protected AuditService $audit) {}

    public function index(Request $request)
    {
        $query = Question::query()
            ->with(['subject', 'quiz', 'owner'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('text', 'like', '%'.$request->string('search').'%');
            })
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->integer('subject_id')))
            ->when($request->filled('quiz_id'), fn ($q) => $q->where('quiz_id', $request->integer('quiz_id')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('difficulty'), fn ($q) => $q->where('difficulty', $request->string('difficulty')))
            ->when($request->filled('status'), function ($q) use ($request) {
                match ($request->string('status')->toString()) {
                    'published' => $q->where('is_published', true)->where('is_archived', false),
                    'draft' => $q->where('is_published', false)->where('is_archived', false),
                    'archived' => $q->where('is_archived', true),
                    default => $q->where('is_archived', false),
                };
            });

        // Guru hanya boleh melihat soal miliknya sendiri; admin melihat semua
        if (auth()->user()->role !== 'admin') {
            $query->where('owner_id', auth()->id());
        }

        $questions = $query->latest()->paginate(15)->withQueryString();

        return view('adventure.admin.questions', [
            'questions' => $questions,
            'subjects' => Subject::orderBy('name')->get(),
            'quizzes' => Quiz::orderBy('title')->get(),
        ]);
    }

    public function create()
    {
        return view('adventure.admin.questions-create', [
            'subjects' => Subject::orderBy('name')->get(),
            'quizzes' => Quiz::orderBy('title')->get(),
            'question' => new Question,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);

        $question = Question::create(array_merge($validated, [
            'owner_id' => auth()->id(),
            'options' => $this->normalizeOptions($validated['options'] ?? null, $validated['type']),
        ]));

        $this->audit->log('question.created', $question, 'Membuat soal baru');

        return redirect()->route('admin.questions')->with('success', 'Soal berhasil dibuat');
    }

    public function edit(Question $question)
    {
        $this->authorizeManage($question);

        return view('adventure.admin.questions-create', [
            'subjects' => Subject::orderBy('name')->get(),
            'quizzes' => Quiz::orderBy('title')->get(),
            'question' => $question,
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $this->authorizeManage($question);

        $validated = $this->validateQuestion($request, $question);

        $question->update(array_merge($validated, [
            'options' => $this->normalizeOptions($validated['options'] ?? null, $validated['type']),
        ]));

        $this->audit->log('question.updated', $question, 'Memperbarui soal');

        return redirect()->route('admin.questions')->with('success', 'Soal berhasil diperbarui');
    }

    public function destroy(Question $question)
    {
        $this->authorizeManage($question);

        $question->delete();
        $this->audit->log('question.deleted', $question, 'Menghapus soal');

        return redirect()->route('admin.questions')->with('success', 'Soal berhasil dihapus');
    }

    public function archive(Question $question)
    {
        $this->authorizeManage($question);

        $question->update(['is_archived' => true, 'is_published' => false]);
        $this->audit->log('question.archived', $question, 'Mengarsipkan soal');

        return redirect()->route('admin.questions')->with('success', 'Soal diarsipkan');
    }

    public function publish(Question $question)
    {
        $this->authorizeManage($question);

        $question->update(['is_published' => true, 'is_archived' => false]);
        $this->audit->log('question.published', $question, 'Mempublikasikan soal');

        return redirect()->route('admin.questions')->with('success', 'Soal dipublikasikan');
    }

    public function duplicate(Question $question)
    {
        $this->authorizeManage($question);

        $copy = $question->replicate();
        $copy->text = $question->text.' (Salinan)';
        $copy->is_published = false;
        $copy->sort_order = $question->sort_order + 1;
        $copy->save();

        $this->audit->log('question.duplicated', $copy, 'Menduplikasi soal');

        return redirect()->route('admin.questions')->with('success', 'Soal diduplikasi');
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:publish,archive,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:questions,id',
        ]);

        $questions = Question::whereIn('id', $validated['ids'])->get();

        if (auth()->user()->role !== 'admin') {
            $questions = $questions->where('owner_id', auth()->id())->values();
        }

        DB::transaction(function () use ($questions, $validated) {
            foreach ($questions as $question) {
                match ($validated['action']) {
                    'publish' => $question->update(['is_published' => true, 'is_archived' => false]),
                    'archive' => $question->update(['is_archived' => true, 'is_published' => false]),
                    'delete' => $question->delete(),
                };
            }
        });

        $this->audit->log('question.bulk_'.$validated['action'], null, 'Aksi massal pada '.count($questions).' soal');

        return redirect()->route('admin.questions')
            ->with('success', count($questions).' soal diproses');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Question::query()->when($request->filled('quiz_id'), fn ($q) => $q->where('quiz_id', $request->integer('quiz_id')));
        if (auth()->user()->role !== 'admin') {
            $query->where('owner_id', auth()->id());
        }

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'subject', 'quiz', 'type', 'text', 'options', 'correct_answer', 'difficulty', 'points', 'time_limit', 'published']);

            $query->with(['subject', 'quiz'])->chunk(200, function ($questions) use ($out) {
                foreach ($questions as $q) {
                    fputcsv($out, [
                        $q->id,
                        $q->subject?->name,
                        $q->quiz?->title,
                        $q->type,
                        $q->text,
                        $q->options ? json_encode($q->options, JSON_UNESCAPED_UNICODE) : '',
                        $q->correct_answer,
                        $q->difficulty,
                        $q->points,
                        $q->time_limit,
                        $q->is_published ? 'yes' : 'no',
                    ]);
                }
            });

            fclose($out);
        }, 'questions-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $rows = array_map('str_getcsv', file($request->file('file')->getRealPath()));
        $header = array_map('trim', array_shift($rows));

        $required = ['text', 'type'];
        $missing = array_diff($required, $header);

        if ($missing) {
            return redirect()->route('admin.questions')
                ->with('error', 'Kolom wajib hilang: '.implode(', ', $missing));
        }

        $created = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $data = array_combine($header, $row);

            if (empty($data['text'])) {
                $errors[] = "Baris ".($i + 2).': teks soal kosong';
                continue;
            }

            $subject = Subject::where('slug', $data['subject_slug'] ?? null)
                ->orWhere('name', $data['subject'] ?? null)
                ->first();

            if (! $subject) {
                $errors[] = "Baris ".($i + 2).": mata pelajaran '".($data['subject'] ?? '?')."' tidak ditemukan";
                continue;
            }

            $options = null;
            if (! empty($data['options'])) {
                $decoded = json_decode($data['options'], true);
                $options = is_array($decoded) ? $decoded : null;
            }

            Question::create([
                'subject_id' => $subject->id,
                'owner_id' => auth()->id(),
                'text' => $data['text'],
                'type' => $data['type'],
                'correct_answer' => $data['correct_answer'] ?? null,
                'explanation' => $data['explanation'] ?? null,
                'difficulty' => $data['difficulty'] ?? 'medium',
                'points' => (int) ($data['points'] ?? 10),
                'time_limit' => (int) ($data['time_limit'] ?? 30),
                'options' => $options,
                'is_published' => false,
            ]);

            $created++;
        }

        $this->audit->log('question.imported', null, "Import {$created} soal");

        $message = "{$created} soal diimpor.";
        if ($errors) {
            $message .= ' '.count($errors).' baris gagal: '.implode('; ', array_slice($errors, 0, 3));
        }

        return redirect()->route('admin.questions')->with('success', $message);
    }

    protected function validateQuestion(Request $request, ?Question $question = null): array
    {
        $rules = [
            'subject_id' => 'required|exists:subjects,id',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'text' => 'required|string|max:5000',
            'type' => 'required|in:'.implode(',', array_keys(config('adventure.question_types'))),
            'correct_answer' => 'nullable|string|max:255',
            'explanation' => 'nullable|string|max:5000',
            'hint' => 'nullable|string|max:255',
            'difficulty' => 'required|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:5|max:300',
            'points' => 'required|integer|min:0|max:1000',
            'image_url' => 'nullable|url|max:500',
            'options' => 'nullable|array',
        ];

        $validated = $request->validate($rules);

        // Pilihan ganda wajib punya minimal 2 opsi dan satu jawaban benar
        $typeConfig = config("adventure.question_types.{$validated['type']}");

        if ($typeConfig['has_options']) {
            $labels = array_filter(array_map('trim', (array) ($validated['options'] ?? [])));

            if (count($labels) < 2) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'options' => 'T minimally dua pilihan jawaban untuk jenis soal ini.',
                ]);
            }

            if (empty($validated['correct_answer'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'correct_answer' => 'Tentukan jawaban yang benar.',
                ]);
            }

            if (! in_array($validated['correct_answer'], $labels, true)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'correct_answer' => 'Jawaban benar harus salah satu dari pilihan yang tersedia.',
                ]);
            }
        }

        if ($validated['type'] === 'short_answer' && empty($validated['correct_answer'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'correct_answer' => 'Jawaban singkat wajib memiliki kunci jawaban.',
            ]);
        }

        return $validated;
    }

    protected function normalizeOptions(?array $options, string $type): ?array
    {
        if (! $type || ! config("adventure.question_types.$type")['has_options']) {
            return null;
        }

        $clean = [];
        foreach ((array) $options as $label => $value) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }
            $clean[$label] = is_string($value) ? trim($value) : $value;
        }

        return $clean ?: null;
    }

    protected function authorizeManage(Question $question): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        abort_unless($question->owner_id === $user->id, 403, 'Anda tidak berhak mengubah soal ini.');
    }
}
