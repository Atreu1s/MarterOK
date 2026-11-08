<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Master extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'title', 'bio', 'services', 'experience_years', 'base_price', 'district', 'schedule', 'is_active'];

    protected function casts(): array
    {
        return ['services' => 'array', 'is_active' => 'boolean', 'base_price' => 'integer', 'experience_years' => 'integer'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(RepairBooking::class);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))->take(2)->map(fn (string $part): string => mb_substr($part, 0, 1))->join('');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! preg_match('/^[a-z0-9-]+$/', $this->slug ?? '')) {
            return null;
        }
        $path = 'images/masters/'.$this->slug.'.webp';

        return is_file(public_path($path)) ? asset($path) : null;
    }

    public function getAvatarThumbnailUrlAttribute(): ?string
    {
        return $this->avatar_url ? asset('images/masters/'.$this->slug.'-thumb.webp') : null;
    }

    public function getWorkExamplesAttribute(): array
    {
        return array_values(array_filter(config('repair.examples', []), fn (array $example): bool => count(array_intersect($this->services, $example['services'])) > 0));
    }
}
