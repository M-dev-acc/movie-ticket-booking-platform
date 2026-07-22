<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovieShow extends Model
{
    protected $fillable = [
        'movie_id',
        'theater_id',
        'screen_id',
        'scheduled_at',
        'duration',
        'price',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (MovieShow $show) {
            $show->end_at = Carbon::parse($show->scheduled_at)->addMinutes($show->duration);
        });
    }

    // public function theater():BelongsTo {
    //     return $this->belongsTo(Theater::class, 'theater_id');
    // }

    public function screen(): BelongsTo
    {
        return $this->belongsTo(Screen::class, 'screen_id');
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }
}
