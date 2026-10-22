<?php
namespace App\Services;

class ScoringService
{
    public function calculateScore(int $basePoints, int $timeLimit, int $responseMs, int $correct): int
    {
        $score = $correct ? $basePoints : 0;
        // Bonus kecepatan: semakin cepat, semakin besar bonus (maks 50% base)
        if ($correct) {
            $speedRatio = 1 - min(($responseMs / ($timeLimit * 1000)), 1);
            $bonus = round($basePoints * 0.5 * $speedRatio);
            $score += $bonus;
        }
        return max(0, $score);
    }

    public function validateAnswer(string $correctAnswer, ?string $submitted): bool
    {
        return $submitted !== null && trim(strtolower($submitted)) === trim(strtolower($correctAnswer));
    }

    public function tieBreak(array $participants): array
    {
        usort($participants, function ($a, $b) {
            if ($a['score'] === $b['score']) {
                return $a['time'] <=> $b['time']; // faster response wins
            }
            return $b['score'] <=> $a['score'];
        });
        return $participants;
    }
}
