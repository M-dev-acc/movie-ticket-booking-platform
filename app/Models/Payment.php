<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const STATUS_INITIATED = 'initiated';
    public const STATUS_CAPTURED = 'captured';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const STATUSES = [
        'initiated',
        'captured',
        'failed',
        'refunded',
    ];

    protected $fillable = [
        'booking_id',
        'gateway',
        'gateway_order_id',
        'gateway_payment_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
