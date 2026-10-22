<?php
namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Quiz;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function create(Request $request)
    {
        $quiz = Quiz::findOrFail($request->quiz_id);
        $code = strtoupper(substr(md5(uniqid()), 0, 8));

        $room = Room::create([
            'code' => $code,
            'quiz_id' => $quiz->id,
            'host_id' => auth()->id(),
            'status' => 'waiting',
        ]);

        return redirect()->route('room.show', $room->code)->with('success', 'Room created: ' . $code);
    }

    public function show($code)
    {
        $room = Room::with('quiz', 'host', 'participants')->where('code', $code)->firstOrFail();
        return view('adventure.room.show', compact('room'));
    }

    public function start($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $session = \App\Models\GameSession::create([
            'room_id' => $room->id,
            'quiz_id' => $room->quiz_id,
            'host_id' => auth()->id(),
            'status' => 'in_progress',
            'mode' => 'live',
            'started_at' => now(),
        ]);
        $room->update(['status' => 'in_progress', 'current_question_index' => 0]);
        return redirect()->route('game.session', $session->id);
    }

    public function join(Request $request, $code)
    {
        $room = Room::where('code', $code)->firstOrFail();

        if ($room->status !== 'waiting') {
            return redirect()->back()->with('error', 'Room is not accepting players');
        }

        $existing = $room->participants()->where('user_id', auth()->id())->exists();
        if (!$existing) {
            $room->participants()->create([
                'user_id' => auth()->id(),
                'display_name' => auth()->user()->name ?? 'Guest',
                'avatar' => auth()->user()->avatar,
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('room.show', $room->code);
    }
}
