<?php
namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug,
            'description' => $this->faker->sentence,
            'color' => '#667eea',
            'icon' => 'book-open',
            'sort_order' => 0,
            'is_active' => true,
            'created_by' => 1,
        ];
    }
}
