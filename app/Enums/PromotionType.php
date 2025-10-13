<?php

namespace App\Enums;

enum PromotionType: string
{
    case BUY_X_GET_Y_FREE = 'buy_x_get_y_free';
    case STEP_DISCOUNT = 'step_discount';
    case FIXED_PRICE_BUNDLE = 'fixed_price_bundle';
    case PERCENTAGE_DISCOUNT = 'percentage_discount';

    public function label(): string
    {
        return match($this) {
            self::BUY_X_GET_Y_FREE => 'Buy X Get Y Free',
            self::STEP_DISCOUNT => 'Step Discount by Item Position',
            self::FIXED_PRICE_BUNDLE => 'Fixed Price Bundle',
            self::PERCENTAGE_DISCOUNT => 'Percentage Discount',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::BUY_X_GET_Y_FREE => 'Buy X items, get Y items free automatically',
            self::STEP_DISCOUNT => 'Different discount percentages based on item position',
            self::FIXED_PRICE_BUNDLE => 'Buy specific quantity for a fixed total price',
            self::PERCENTAGE_DISCOUNT => 'Discount percentage applied to the total price',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
