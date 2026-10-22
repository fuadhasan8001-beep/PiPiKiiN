<?php
namespace App\Http\Controllers;

use App\Services\ScoringService;
use App\Models\Room;
use App\Models\Answer;
use App\Models\GameSession;
use Illuminate\Http\Request;

class GameSessionController extends Controller
{
    public function index($sessionId)
    {
        $session = GameSession::with('room.quiz', 'room.participants')->findOrFail($sessionId);
        return view('adventure.game.session', compact('session'));
    }

    public function start(Request $request, $sessionId)
    {
        $session = GameSession::findOrFail($sessionId);
        $session->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
        $session->room->update(['status' => 'in_progress']);
        return back()->with('success', 'Session started');
    }

    public function submitAnswer(Request $request, $sessionId)
    {
        $session = GameSession::findOrFail($sessionId);
        $service = new ScoringService();

        $validated = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'answer_text' => 'nullable|string|max:500',
        ]);

        $question = \App\Models\Question::findOrFail($validated['question_id']);

        $existing = \App\Models\Answer::where('room_id', $session->room_id)
            ->where('user_id', auth()->id())
            ->where('question_id', $question->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Answer already recorded');
        }

        $isCorrect = $service->validateAnswer(
            $question->correct_answer ?? '',
            $validated['answer_text'] ?? null
        );

        $responseMs = (int) ($request->input('response_ms') ?? $question->time_limit * 1000);
        $points = $service->calculateScore($question->points, $question->time_limit, $responseMs, $isCorrect ? 1 : 0);

        \App\Models\Answer::create([
            'room_id' => $session->room_id,
            'user_id' => auth()->id(),
            'question_id' => $question->id,
            'answer_text' => $validated['answer_text'] ?? null,
            'is_correct' => $isCorrect,
            'points_awarded' => $points,
            'response_ms' => $responseMs,
            'submitted_at' => now(),
        ]);

        $participant = $session->room->participants()->where('user_id', auth()->id())->first();
        if ($participant) {
            $participant->increment('score', $points);
            if ($isCorrect) $participant->increment('correct_count');
        }

        return back()->with('success', 'Answer submitted');
    }

    public function finish($sessionId)
    {
        $session = GameSession::findOrFail($sessionId);
        $session->update([
            'status' => 'finished',
            'finished_at' => now(),
        ]);
        $session->room->update(['status' => 'finished', 'finished_at' => now()]);
        return redirect()->route('dashboard')->with('success', 'Session finished');
    }
}
