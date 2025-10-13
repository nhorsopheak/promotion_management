<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'tax_amount',
        'total',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'payment_method',
        'payment_status',
        'paid_at',
        'applied_promotions',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'applied_promotions' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function promotionLogs(): HasMany
    {
        return $this->hasMany(PromotionLog::class);
    }

    public function luckyDrawEntries(): HasMany
    {
        return $this->hasMany(LuckyDrawEntry::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'used_in_order_id');
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->final_price * $item->quantity;
        });

        $this->discount_amount = $this->items->sum('discount_amount');

        $this->total = $this->subtotal - $this->discount_amount + $this->shipping_fee + $this->tax_amount;
    }
}
