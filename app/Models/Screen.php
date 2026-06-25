<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screen extends Model
{
    use SoftDeletes;

    public const TYPE_2D = '2d';
    public const TYPE_3D = '3d';
    public const TYPE_IMAX_2D = 'imax_2d';
    public const TYPE_IMAX_3D = 'imax_3d';
    public const TYPE_IMAX_LASER_2D = 'imax_laser_2d';
    public const TYPE_IMAX_LASER_3D = 'imax_laser_3d';
    public const TYPE_PXL_2D = 'pxl_2d';
    public const TYPE_PXL_3D = 'pxl_3d';
    public const TYPE_4DX_3D = '4dx_3d';
    public const TYPE_SCREENX_2D = 'screenx_2d';
    public const TYPE_INSIGNIA_2D = 'insignia_2d';
    public const TYPE_LUXE_2D = 'luxe_2d';

    public const TYPES = [
        self::TYPE_2D,
        self::TYPE_3D,
        self::TYPE_IMAX_2D,
        self::TYPE_IMAX_3D,
        self::TYPE_IMAX_LASER_2D,
        self::TYPE_IMAX_LASER_3D,
        self::TYPE_PXL_2D,
        self::TYPE_PXL_3D,
        self::TYPE_4DX_3D,
        self::TYPE_SCREENX_2D,
        self::TYPE_INSIGNIA_2D,
        self::TYPE_LUXE_2D,
    ];

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
