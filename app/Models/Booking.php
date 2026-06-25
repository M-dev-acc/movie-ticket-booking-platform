<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected array $fillable = [
        'user_id',
        'show_id',
        'booked_at',
        'status',
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function show() : BelongsTo {
        return $this->belongsTo(MovieShow::class, 'show_id');
    }

    public function movie() : BelongsTo {
        return $this->belongsTo(Movie::class, 'movie_id');
    }
}
