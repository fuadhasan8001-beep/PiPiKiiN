<?php

namespace Database\Factories;

use App\Models\QuizAttempt;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttemptAnswerFactory extends Factory
{
    protected $model = \App\Models\AttemptAnswer::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'answer_text' => $this->faker->sentence,
            'is_correct' => mt_rand(0, 1),
            'points_awarded' => 0,
        ];
    }
}
