<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'promotion_id',
        'amount',
        'type',
        'valid_from',
        'valid_until',
        'is_used',
        'used_in_order_id',
        'used_at',
        'minimum_spend',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'minimum_spend' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function usedInOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'used_in_order_id');
    }

    public function isValid(): bool
    {
        if ($this->is_used || $this->status !== 'active') {
            return false;
        }

        $now = Carbon::now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    public function canBeUsedForAmount(float $amount): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->minimum_spend && $amount < $this->minimum_spend) {
            return false;
        }

        return true;
    }

    public static function generateCode(): string
    {
        return 'VCH-' . strtoupper(substr(uniqid(), -8));
    }

    public function markAsUsed(Order $order): void
    {
        $this->update([
            'is_used' => true,
            'used_in_order_id' => $order->id,
            'used_at' => Carbon::now(),
            'status' => 'used',
        ]);
    }
}
