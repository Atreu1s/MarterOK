<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return ['article_id' => Article::factory()->published(), 'user_id' => User::factory(), 'body' => fake()->paragraph(), 'is_approved' => false];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => ['is_approved' => true]);
    }
}
