<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'order_id',
        'user_id',
        'action',
        'discount_amount',
        'affected_items',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'affected_items' => 'array',
        'metadata' => 'array',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
