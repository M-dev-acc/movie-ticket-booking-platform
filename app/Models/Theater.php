<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Theater extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'status',
    ];

    public function screens() : HasMany {
        return $this->hasMany(Screen::class, 'screen_id');
    }

    public function shows() : HasManyThrough {
        return $this->hasManyThrough(MovieShow::class, Screen::class);
    }
}
