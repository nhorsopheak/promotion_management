<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'product_id',
        'category_id',
        'type',
        'gift_quantity',
        'gift_stock',
    ];

    protected $casts = [
        'gift_quantity' => 'integer',
        'gift_stock' => 'integer',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
