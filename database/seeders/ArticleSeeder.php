<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'moderator@pochin.test')->firstOrFail();
        foreach (json_decode(File::get(database_path('seeders/data/articles.json')), true, 512, JSON_THROW_ON_ERROR) as $index => $article) {
            Article::firstOrCreate(['title' => $article['title']], $article + ['user_id' => $author->id, 'status' => 'published', 'created_at' => now()->subDays($index + 1)]);
        }
    }
}
