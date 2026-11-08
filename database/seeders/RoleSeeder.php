<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $reader = Role::firstOrCreate(['name' => 'reader'], ['label' => 'Читатель']);
        $moderator = Role::firstOrCreate(['name' => 'moderator'], ['label' => 'Модератор']);
        $user = User::firstOrCreate(['email' => 'moderator@pochin.test'], ['name' => 'Модератор', 'password' => env('DEMO_MODERATOR_PASSWORD', 'RepairDemo!2026')]);
        $user->forceFill(['role_id' => $moderator->id])->save();
        $user = User::firstOrCreate(['email' => 'reader@pochin.test'], ['name' => 'Клиент', 'password' => env('DEMO_READER_PASSWORD', 'ReaderDemo!2026')]);
        if (! $user->role_id) {
            $user->forceFill(['role_id' => $reader->id])->save();
        }
    }
}
