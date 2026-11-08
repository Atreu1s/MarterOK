<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'excerpt', 'body', 'category', 'cover', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->cover ? asset(str_starts_with($this->cover, 'images/') ? $this->cover : 'storage/'.$this->cover) : asset('images/cooling-detail.webp');
    }

    public function getReadingMinutesAttribute(): int
    {
        return max(1, (int) ceil(mb_strlen($this->body) / 1100));
    }
}
