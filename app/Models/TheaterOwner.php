<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TheaterOwner extends Model
{
    protected array $fillable = [
        'theater_id',
        'user_id',
        'assgined_by',
    ];

    public function theater() : BelongsToMany {
        return $this->belongsToMany(Theater::class, 'theater_owners', 'theaeter_id')
        ->withPivot('assigned_by')
        ->withTimestamps();
    }

}
