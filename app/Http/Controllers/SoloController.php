<?php
namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use App\Models\RoomParticipant;
use Illuminate\Http\Request;

class SoloController extends Controller
{
    public function play(Request $request)
    {
        $quizzes = Quiz::query()
            ->with('subject')
            ->where('is_published', true)
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->integer('subject_id')))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%'))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('adventure.play', [
            'quizzes' => $quizzes,
            'subjects' => Subject::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function start(int $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        abort_unless($quiz->is_published, 404);

        return \Livewire\Livewire::mount('game.solo-practice', ['quiz' => $quiz]);
    }

    public function history()
    {
        $attempts = QuizAttempt::with('quiz')
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->latest()
            ->paginate(15);

        return view('adventure.history', compact('attempts'));
    }

    public function results(int $attemptId)
    {
        $attempt = QuizAttempt::with(['quiz', 'answers.question'])
            ->where('user_id', auth()->id())
            ->findOrFail($attemptId);

        return view('adventure.results', compact('attempt'));
    }

    public function leaderboard()
    {
        $participants = RoomParticipant::query()
            ->with('user', 'room.quiz')
            ->where('status', 'active')
            ->orderByDesc('score')
            ->limit(50)
            ->get();

        return view('adventure.leaderboard', compact('participants'));
    }
}
