<?php
namespace App\Policies;

use App\Models\User;

class QuizPolicy
{
    public function manage(User $user, $quiz = null): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'teacher' && $quiz) {
            return $quiz->owner_id === $user->id || $quiz->subject?->created_by === $user->id;
        }
        return false;
    }

    public function manageAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'teacher']);
    }
}
