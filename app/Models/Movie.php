<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'external_id',
        'title',
        'poster_path',
        'release_date',
        'genres',
        'rating',
        'original_language',
        'overview',
    ];

    protected $casts = [
        'genres' => 'array',
        'release_date' => 'datetime',
    ];
}
