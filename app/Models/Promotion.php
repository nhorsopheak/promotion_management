<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Enums\PromotionStatus;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'days_of_week',
        'conditions',
        'benefits',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'days_of_week' => 'array',
        'conditions' => 'array',
        'benefits' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($promotion) {
            if (empty($promotion->status)) {
                $promotion->status = PromotionStatus::ACTIVE->value;
            }
        });
    }

    public function promotionProducts(): HasMany
    {
        return $this->hasMany(PromotionProduct::class);
    }
    public function promotionLogs(): HasMany
    {
        return $this->hasMany(PromotionLog::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function luckyDrawEntries(): HasMany
    {
        return $this->hasMany(LuckyDrawEntry::class);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = Carbon::now();

        // Check date range
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function isValidForTime(Carbon $dateTime = null): bool
    {
        // Simplified - no time or day restrictions, only date range validation
        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
