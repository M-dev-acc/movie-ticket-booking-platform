<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_STANDARD = 'standard';
    public const TYPE_PREMIUM = 'premium';
    public const TYPE_RECLINER = 'recliner';

    public const TYPES = [
        self::TYPE_STANDARD,
        self::TYPE_PREMIUM,
        self::TYPE_RECLINER,
    ];

    protected $fillable = [
        'screen_id',
        'number',
        'row',
        'is_available',
    ];

    public function screen() : BelongsTo {
        return $this->belongsTo(Screen::class);
    }
    public function theater() : HasOneThrough {
        return $this->hasOneThrough(Theater::class, Screen::class, 'screen_id', 'theater_id');
    }
}
