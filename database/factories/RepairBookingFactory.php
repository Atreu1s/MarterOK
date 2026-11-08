<?php

namespace Database\Factories;

use App\Models\Master;
use App\Models\RepairBooking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RepairBookingFactory extends Factory
{
    protected $model = RepairBooking::class;

    public function definition(): array
    {
        return ['submission_key' => (string) Str::uuid(), 'user_id' => User::factory(), 'master_id' => Master::factory(), 'service' => 'diagnostics', 'device' => 'Учебный ноутбук 14', 'description' => 'Не включается после завершения работы. Индикатор зарядки горит.', 'contact_name' => fake()->name(), 'phone' => '+79990000000', 'photos' => [], 'status' => 'new', 'consented_at' => now()];
    }
}
