<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateModerator extends Command
{
    protected $signature = 'pochin:moderator {email}';

    protected $description = 'Назначить существующего пользователя модератором';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        if (Validator::make(['email' => $email], ['email' => ['required', 'email']])->fails()) {
            $this->error('Укажите корректный email.');

            return self::FAILURE;
        }
        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error('Пользователь не найден. Сначала зарегистрируйте его на сайте.');

            return self::FAILURE;
        }
        $role = Role::firstOrCreate(['name' => 'moderator'], ['label' => 'Модератор']);
        $user->forceFill(['role_id' => $role->id, 'is_blocked' => false])->save();
        $this->info('Права модератора назначены пользователю '.$email.'.');

        return self::SUCCESS;
    }
}
