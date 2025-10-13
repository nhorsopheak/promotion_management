<?php

namespace App\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED_AMOUNT = 'fixed_amount';
    case FIXED_PRICE = 'fixed_price';
    case FREE_ITEM = 'free_item';

    public function label(): string
    {
        return match($this) {
            self::PERCENTAGE => 'Percentage Off',
            self::FIXED_AMOUNT => 'Fixed Amount Off',
            self::FIXED_PRICE => 'Fixed Price',
            self::FREE_ITEM => 'Free Item',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
