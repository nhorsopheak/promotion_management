<?php

namespace Database\Factories;

use App\Models\Promotion;
use App\Enums\PromotionType;
use App\Enums\PromotionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('PROMO-####')),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(array_column(PromotionType::cases(), 'value')),
            'status' => $this->faker->randomElement(array_column(PromotionStatus::cases(), 'value')),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'start_time' => null,
            'end_time' => null,
            'days_of_week' => null,
            'conditions' => [
                'discount_type' => 'percentage',
                'discount_value' => $this->faker->numberBetween(5, 50),
                'apply_to_type' => 'all',
            ],
        ];
    }

    /**
     * Indicate that the promotion is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PromotionStatus::ACTIVE->value,
        ]);
    }

    /**
     * Indicate that the promotion is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PromotionStatus::DRAFT->value,
        ]);
    }

    /**
     * Indicate that the promotion is a percentage discount.
     */
    public function percentageDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PromotionType::PERCENTAGE_DISCOUNT->value,
            'conditions' => [
                'discount_type' => 'percentage',
                'discount_value' => $this->faker->numberBetween(5, 50),
                'apply_to_type' => 'all',
            ],
        ]);
    }

    /**
     * Indicate that the promotion is buy X get Y free.
     */
    public function buyXGetYFree(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PromotionType::BUY_X_GET_Y_FREE->value,
            'conditions' => [
                'buy_quantity' => 2,
                'get_quantity' => 1,
                'apply_to_type' => 'any',
                'get_type' => 'cheapest',
                'apply_to_cheapest' => true,
            ],
        ]);
    }

    /**
     * Indicate that the promotion is a step discount.
     */
    public function stepDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PromotionType::STEP_DISCOUNT->value,
            'conditions' => [
                'discount_tiers' => [
                    ['position' => 2, 'percentage' => 20],
                    ['position' => 3, 'percentage' => 30],
                    ['position' => 5, 'percentage' => 50],
                ],
            ],
        ]);
    }

    /**
     * Indicate that the promotion is a fixed price bundle.
     */
    public function fixedPriceBundle(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PromotionType::FIXED_PRICE_BUNDLE->value,
            'conditions' => [
                'bundle_quantity' => 3,
                'bundle_price' => 99.99,
                'bundle_type' => 'any',
            ],
        ]);
    }
}
