<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screen extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'theater_id',
        'type',
        'capacity',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function theater(): BelongsTo {
        return $this->belongsTo(Theater::class, 'theater_id');
    }

    public function shows() : HasMany {
        return $this->hasMany(MovieShow::class, 'screen_id');
    }

    public function seats() : HasMany {
        return $this->hasMany(Seat::class);
    }
}
