<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'is_blocked' => 'boolean', 'email_verified_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            $user->role_id ??= Role::firstOrCreate(['name' => 'reader'], ['label' => 'Читатель'])->id;
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(RepairBooking::class);
    }

    public function isModerator(): bool
    {
        return ! $this->is_blocked && $this->role?->name === 'moderator';
    }
}
