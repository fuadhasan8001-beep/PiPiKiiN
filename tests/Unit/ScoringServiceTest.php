<?php
use App\Services\ScoringService;

beforeEach(function () {
    $this->service = new ScoringService();
});

test('correct answer gets base points', function () {
    $score = $this->service->calculateScore(10, 30, 5000, true);
    expect($score)->toBeGreaterThanOrEqual(10);
});

test('wrong answer gives zero', function () {
    $score = $this->service->calculateScore(10, 30, 5000, false);
    expect($score)->toBe(0);
});

test('tie break prefers faster response', function () {
    $items = [
        ['name' => 'A', 'score' => 30, 'time' => 5000],
        ['name' => 'B', 'score' => 30, 'time' => 3000],
    ];
    $sorted = $this->service->tieBreak($items);
    expect($sorted[0]['name'])->toBe('B');
});
