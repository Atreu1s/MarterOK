<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class RepairBooking extends Model
{
    use HasFactory;

    public const STATUSES = ['new' => 'Новая заявка', 'accepted' => 'Мастер назначен', 'diagnosing' => 'На диагностике', 'approval' => 'Ждёт согласования', 'repairing' => 'В ремонте', 'ready' => 'Можно забирать', 'closed' => 'Завершена', 'cancelled' => 'Отменена'];

    public const TRANSITIONS = ['new' => ['accepted', 'cancelled'], 'accepted' => ['diagnosing', 'cancelled'], 'diagnosing' => ['approval', 'cancelled'], 'approval' => ['repairing', 'cancelled'], 'repairing' => ['ready'], 'ready' => ['closed'], 'closed' => [], 'cancelled' => []];

    protected $fillable = ['submission_key', 'user_id', 'master_id', 'service', 'device', 'description', 'contact_name', 'phone', 'photos', 'status', 'quoted_price', 'quote_accepted_at', 'public_message', 'internal_note', 'consented_at'];

    protected $hidden = ['internal_note', 'submission_key'];

    protected function casts(): array
    {
        return ['photos' => 'array', 'quoted_price' => 'integer', 'quote_accepted_at' => 'datetime', 'consented_at' => 'datetime'];
    }

    public function save(array $options = []): bool
    {
        return DB::transaction(fn (): bool => parent::save($options));
    }

    protected static function booted(): void
    {
        static::created(function (RepairBooking $repair): void {
            $repair->events()->create(['actor' => 'Клиент', 'status' => 'new', 'message' => 'Заявка отправлена. Мастер уточнит детали перед ремонтом.']);
        });
        static::updated(function (RepairBooking $repair): void {
            if ($repair->wasChanged(['status', 'public_message', 'quoted_price', 'master_id'])) {
                $repair->events()->create(['actor' => auth()->user()?->isModerator() ? 'Модератор' : 'Клиент', 'status' => $repair->status, 'message' => $repair->public_message, 'quoted_price' => $repair->quoted_price]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(RepairEvent::class)->orderBy('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status];
    }

    public function getServiceLabelAttribute(): string
    {
        return config('repair.services.'.$this->service.'.title', 'Диагностика');
    }
}
