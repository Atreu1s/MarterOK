<?php

namespace Database\Factories;

use App\Models\RepairBooking;
use App\Models\RepairEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepairEventFactory extends Factory
{
    protected $model = RepairEvent::class;

    public function definition(): array
    {
        return ['repair_booking_id' => RepairBooking::factory(), 'actor' => 'Клиент', 'status' => 'new', 'message' => 'Заявка отправлена.'];
    }
}
