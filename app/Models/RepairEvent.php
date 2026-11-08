<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairEvent extends Model
{
    use HasFactory;

    protected $fillable = ['actor', 'status', 'message', 'quoted_price'];

    public function repair(): BelongsTo
    {
        return $this->belongsTo(RepairBooking::class, 'repair_booking_id');
    }
}
