<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TheaterOwner extends Model
{
    protected array $fillable = [
        'theater_id',
        'user_id',
        'assigned_by',
    ];

    public function theater() : BelongsToMany {
        return $this->belongsToMany(Theater::class, 'theater_owners', 'theater_id')
        ->withPivot('assigned_by')
        ->withTimestamps();
    }

    public function owner() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function assignedBy() : BelongsTo {
        return $this->belongsTo(User::class);
    }
}
