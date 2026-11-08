<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return ['user_id' => User::factory(), 'title' => fake()->sentence(5), 'excerpt' => fake()->paragraph(), 'body' => fake()->paragraphs(5, true), 'category' => fake()->randomElement(['Уход', 'Ремонт', 'Выбор техники']), 'cover' => 'images/cooling-detail.webp', 'status' => 'draft'];
    }

    public function published(): static
    {
        return $this->state(fn (): array => ['status' => 'published']);
    }
}
