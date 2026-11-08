<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function moderator(): User
    {
        $role = Role::firstOrCreate(['name' => 'moderator'], ['label' => 'Модератор']);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
