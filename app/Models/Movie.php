<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'poster',
        'release_date',
        'genres',
        'original_language',
    ];

    protected $casts = [
        'geners' => 'array',
        'release_date' => 'datetime',
    ];
}
