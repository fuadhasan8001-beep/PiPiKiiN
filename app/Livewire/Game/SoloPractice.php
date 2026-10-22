<?php
namespace App\Livewire\Game;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Services\ScoringService;
use Livewire\Component;

class SoloPractice extends Component
{
    public Quiz $quiz;

    public ?QuizAttempt $attempt = null;

    /** @var array<int, array> */
    public array $questions = [];

    public int $index = 0;

    public ?string $answer = null;

    public ?int $lastPoints = null;

    public ?bool $lastCorrect = null;

    public ?string $lastExplanation = null;

    public ?int $remainingSeconds = null;

    public int $serverNow = 0;

    public bool $finished = false;

    public function mount(Quiz $quiz): void
    {
        abort_unless($quiz->is_published, 404);

        $this->quiz = $quiz;
        $this->serverNow = now()->getTimestampMs();

        $this->questions = $quiz->questions()
            ->where('is_published', true)
            ->where('is_archived', false)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Question $q) => [
                'id' => $q->id,
                'text' => $q->text,
                'type' => $q->type,
                'options' => $q->options ?? [],
                'time_limit' => $q->time_limit,
                'points' => $q->points,
                'image_url' => $q->image_url,
            ])
            ->all();

        abort_if(empty($this->questions), 404, 'Kuis ini belum memiliki soal.');

        $this->startAttempt();
    }

    protected function startAttempt(): void
    {
        $this->attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $this->quiz->id,
            'mode' => 'solo',
            'status' => 'in_progress',
            'total_questions' => count($this->questions),
            'started_at' => now(),
        ]);
    }

    public function submit(): void
    {
        if ($this->finished || $this->lastPoints !== null) {
            return;
        }

        $question = $this->currentQuestion();
        if (! $question) {
            return;
        }

        $service = new ScoringService();
        $isCorrect = $service->validateAnswer(
            $this->correctAnswerFor($question['id']),
            $this->answer
        );

        $elapsed = max(0, (int) ((now()->getTimestampMs() - $this->serverNow) / 1000));
        $responseMs = min($elapsed * 1000, $question['time_limit'] * 1000);
        $points = $service->calculateScore(
            $question['points'],
            $question['time_limit'],
            $responseMs,
            $isCorrect ? 1 : 0
        );

        $this->attempt->answers()->create([
            'question_id' => $question['id'],
            'answer_text' => $this->answer,
            'is_correct' => $isCorrect,
            'points_awarded' => $points,
        ]);

        $this->attempt->increment('score', $points);
        if ($isCorrect) {
            $this->attempt->increment('correct_count');
        }

        $this->lastPoints = $points;
        $this->lastCorrect = $isCorrect;
        $this->lastExplanation = Question::find($question['id'])?->explanation;
    }

    public function next(): void
    {
        if ($this->index >= count($this->questions) - 1) {
            $this->finish();
            return;
        }

        $this->index++;
        $this->answer = null;
        $this->lastPoints = null;
        $this->lastCorrect = null;
        $this->lastExplanation = null;
        $this->serverNow = now()->getTimestampMs();
    }

    public function finish(): void
    {
        if ($this->finished) {
            return;
        }

        $this->finished = true;
        $total = max(1, $this->attempt->total_questions);
        $this->attempt->update([
            'status' => 'completed',
            'accuracy' => round(($this->attempt->correct_count / $total) * 100, 2),
            'duration_seconds' => $this->attempt->started_at?->diffInSeconds(now()) ?? 0,
            'completed_at' => now(),
        ]);
    }

    public function currentQuestion(): ?array
    {
        return $this->questions[$this->index] ?? null;
    }

    /**
     * Kunci jawaban hanya diambil dari server saat submit, tidak pernah
     * dikirim ke klien sebelum jawaban peserta tercatat.
     */
    protected function correctAnswerFor(int $questionId): string
    {
        return Question::whereKey($questionId)->value('correct_answer') ?? '';
    }

    public function render()
    {
        return view('livewire.game.solo-practice', [
            'question' => $this->currentQuestion(),
            'total' => count($this->questions),
        ]);
    }
}
