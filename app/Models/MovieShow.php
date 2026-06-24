<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovieShow extends Model
{
    protected $fillable = [
        'movie_id',
        'theater_id',
        'screen_id',
        'start_at',
        'duration',
        'price',
    ];

    protected $casts = [
        'start_at' => 'datetime',
    ];

    public function theater():BelongsTo {
        return $this->belongsTo(Theater::class, 'theater_id');
    }

    public function screen() : BelongsTo {
        return $this->belongsTo(Screen::class, 'screen_id');
    }

    public function movie() : BelongsTo {
        return $this->belongsTo(Movie::class, 'movie_id');
    }
}
