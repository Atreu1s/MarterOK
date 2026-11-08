<?php

namespace Database\Factories;

use App\Models\Master;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterFactory extends Factory
{
    protected $model = Master::class;

    public function definition(): array
    {
        return ['name' => fake()->name(), 'slug' => fake()->unique()->slug(3), 'title' => 'Ремонт ноутбуков', 'bio' => fake()->paragraphs(3, true), 'services' => array_keys(config('repair.services')), 'experience_years' => fake()->numberBetween(1, 20), 'base_price' => fake()->numberBetween(500, 3000), 'district' => 'Басманный район', 'schedule' => 'Пн–Пт, 10:00–19:00', 'is_active' => true];
    }
}
