<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuckyDrawEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'promotion_id',
        'order_id',
        'user_id',
        'draw_date',
        'is_winner',
        'prize',
        'won_at',
        'status',
    ];

    protected $casts = [
        'draw_date' => 'datetime',
        'is_winner' => 'boolean',
        'won_at' => 'datetime',
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

    public static function generateTicketNumber(): string
    {
        return 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }

    public function markAsWinner(string $prize): void
    {
        $this->update([
            'is_winner' => true,
            'prize' => $prize,
            'won_at' => now(),
            'status' => 'won',
        ]);
    }
}
