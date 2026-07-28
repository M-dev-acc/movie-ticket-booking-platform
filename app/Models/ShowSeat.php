<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;
use Spatie\Permission\Commands\Show;

class ShowSeat extends Model
{
    protected $fillable = [
        'show_id',
        'seat_id',
        'status',
        'locked_until',
        'price',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }
}
