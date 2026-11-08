<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoleSeeder::class, MasterSeeder::class, ArticleSeeder::class]);
        if (! Article::where('category', 'Примеры Faker')->exists()) {
            Article::factory()->count(3)->create(['user_id' => User::where('email', 'moderator@pochin.test')->value('id'), 'category' => 'Примеры Faker', 'status' => 'draft']);
        }
    }
}
