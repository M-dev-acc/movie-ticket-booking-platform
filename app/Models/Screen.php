<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Screen extends Model
{
    public function theater(): BelongsTo {
        return $this->belongsTo(Theater::class, 'theater_id');
    }

    public function shows() : HasMany {
    return $this->hasMany(MovieShow::class, 'screen_id');
    }
}
