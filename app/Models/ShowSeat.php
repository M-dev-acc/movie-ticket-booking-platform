<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShowSeat extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_LOCKED = 'locked';
    public const STATUS_BOOKED = 'booked';

    public const STATUSES = [
        self::STATUS_AVAILABLE,
        self::STATUS_LOCKED,
        self::STATUS_BOOKED,
    ];

    protected $fillable = [
        'show_id',
        'seat_id',
        'status',
        'locked_until',
        'price',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function show(): BelongsTo
    {
        return $this->belongsTo(MovieShow::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where(function (Builder $query): Builder {
            return $query->where('status', self::STATUS_AVAILABLE)
                ->whereNull('locked_until');
        });
    }
    
    public function scopeLocked(Builder $query): Builder
    {
        return $query->where(function (Builder $query): Builder {
            return $query->where('status', self::STATUS_LOCKED)
                ->whereNotNull('locked_until');
        });
    }

    public function scopeExpiredLockedSeats(Builder $query): Builder
    {
        return $query->where(function (Builder $query): Builder {
            return $query->where('status', self::STATUS_LOCKED)
                ->where('locked_until', '<', now());
        });
    }
}
