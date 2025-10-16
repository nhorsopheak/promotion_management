<?php

namespace App\Services;

use App\Models\Promotion;
use App\Services\Promotions\PromotionEngine;
use Illuminate\Validation\ValidationException;

class PromotionService
{
    public function __construct(
        protected PromotionEngine $promotionEngine
    ) {}

    /**
     * Create a new promotion with validation
     */
    public function createPromotion(array $data): Promotion
    {
        // Validate the promotion configuration using the strategy
        $this->validatePromotionConfig($data);

        // Create the promotion
        return Promotion::create($data);
    }

    /**
     * Validate promotion configuration using the appropriate strategy
     */
    protected function validatePromotionConfig(array $data): void
    {
        $type = $data['type'] ?? null;
        $conditions = $data['conditions'] ?? [];

        if (!$type) {
            throw ValidationException::withMessages([
                'type' => ['Promotion type is required']
            ]);
        }

        // Get the strategy for this promotion type
        $strategy = $this->promotionEngine->getStrategy($type);

        if (!$strategy) {
            throw ValidationException::withMessages([
                'type' => ['Invalid promotion type or strategy not found']
            ]);
        }

        // Validate the configuration using the strategy
        if (!$strategy->validateConfig($conditions)) {
            throw ValidationException::withMessages([
                'conditions' => [$this->getValidationMessage($type, $conditions)]
            ]);
        }

        // Additional type-specific validation
        $this->validateTypeSpecificRules($type, $conditions);
    }

    /**
     * Get validation error message based on promotion type
     */
    protected function getValidationMessage(string $type, array $conditions): string
    {
        return match($type) {
            'buy_x_get_y_free' => $this->getBuyXGetYFreeValidationMessage($conditions),
            'step_discount' => $this->getStepDiscountValidationMessage($conditions),
            'fixed_price_bundle' => $this->getFixedPriceBundleValidationMessage($conditions),
            'percentage_discount' => $this->getPercentageDiscountValidationMessage($conditions),
            default => 'Invalid promotion configuration'
        };
    }

    /**
     * Validate type-specific rules
     */
    protected function validateTypeSpecificRules(string $type, array $conditions): void
    {
        switch ($type) {
            case 'buy_x_get_y_free':
                $this->validateBuyXGetYFree($conditions);
                break;
            case 'step_discount':
                $this->validateStepDiscount($conditions);
                break;
            case 'fixed_price_bundle':
                $this->validateFixedPriceBundle($conditions);
                break;
            case 'percentage_discount':
                $this->validatePercentageDiscount($conditions);
                break;
        }
    }

    /**
     * Validate Buy X Get Y Free configuration
     */
    protected function validateBuyXGetYFree(array $conditions): void
    {
        $errors = [];

        if (!isset($conditions['buy_quantity']) || $conditions['buy_quantity'] <= 0) {
            $errors['conditions.buy_quantity'] = ['Buy quantity must be greater than 0'];
        }

        if (!isset($conditions['get_quantity']) || $conditions['get_quantity'] <= 0) {
            $errors['conditions.get_quantity'] = ['Get quantity must be greater than 0'];
        }

        // Validate apply_to_type
        $applyToType = $conditions['apply_to_type'] ?? null;
        if ($applyToType && !in_array($applyToType, ['any', 'specific_products', 'specific_categories'])) {
            $errors['conditions.apply_to_type'] = ['Apply to type must be: any, specific_products, or specific_categories'];
        }

        // Validate product/category IDs if specified
        if ($applyToType === 'specific_products' && empty($conditions['apply_to_product_ids'])) {
            $errors['conditions.apply_to_product_ids'] = ['Product ID is required when apply_to_type is specific_products'];
        }

        if ($applyToType === 'specific_categories' && empty($conditions['apply_to_category_ids'])) {
            $errors['conditions.apply_to_category_ids'] = ['Category ID is required when apply_to_type is specific_categories'];
        }

        // Validate get_type
        $getType = $conditions['get_type'] ?? null;
        if ($getType && !in_array($getType, ['cheapest', 'specific_products'])) {
            $errors['conditions.get_type'] = ['Get type must be: cheapest or specific_products'];
        }

        if ($getType === 'specific_products' && empty($conditions['get_product_ids'])) {
            $errors['conditions.get_product_ids'] = ['Product ID is required when get_type is specific_products'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Validate Step Discount configuration
     */
    protected function validateStepDiscount(array $conditions): void
    {
        $errors = [];

        if (!isset($conditions['discount_tiers']) || !is_array($conditions['discount_tiers']) || empty($conditions['discount_tiers'])) {
            $errors['conditions.discount_tiers'] = ['Discount tiers are required and must be a non-empty array'];
        } else {
            foreach ($conditions['discount_tiers'] as $index => $tier) {
                if (!isset($tier['position']) || $tier['position'] < 2) {
                    $errors["conditions.discount_tiers.{$index}.position"] = ['Position must be 2 or greater'];
                }
                if (!isset($tier['percentage']) || $tier['percentage'] < 0 || $tier['percentage'] > 100) {
                    $errors["conditions.discount_tiers.{$index}.percentage"] = ['Percentage must be between 0 and 100'];
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Validate Fixed Price Bundle configuration
     */
    protected function validateFixedPriceBundle(array $conditions): void
    {
        $errors = [];

        if (!isset($conditions['bundle_quantity']) || $conditions['bundle_quantity'] < 2) {
            $errors['conditions.bundle_quantity'] = ['Bundle quantity must be 2 or greater'];
        }

        if (!isset($conditions['bundle_price']) || $conditions['bundle_price'] <= 0) {
            $errors['conditions.bundle_price'] = ['Bundle price must be greater than 0'];
        }

        // Validate bundle_type
        $bundleType = $conditions['bundle_type'] ?? null;
        if ($bundleType && !in_array($bundleType, ['any', 'specific_products', 'specific_categories'])) {
            $errors['conditions.bundle_type'] = ['Bundle type must be: any, specific_products, or specific_categories'];
        }

        // Validate product/category IDs if specified
        if ($bundleType === 'specific_products' && empty($conditions['eligible_product_ids'])) {
            $errors['conditions.eligible_product_ids'] = ['Product ID is required when bundle_type is specific_products'];
        }

        if ($bundleType === 'specific_categories' && empty($conditions['eligible_category_ids'])) {
            $errors['conditions.eligible_category_ids'] = ['Category ID is required when bundle_type is specific_categories'];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Validate Percentage Discount configuration
     */
    protected function validatePercentageDiscount(array $conditions): void
    {
        $errors = [];

        // Validate discount_type
        $discountType = $conditions['discount_type'] ?? null;
        if (!$discountType || !in_array($discountType, ['percentage', 'fixed_amount'])) {
            $errors['conditions.discount_type'] = ['Discount type must be: percentage or fixed_amount'];
        }

        // Validate discount_value
        if (!isset($conditions['discount_value']) || $conditions['discount_value'] <= 0) {
            $errors['conditions.discount_value'] = ['Discount value must be greater than 0'];
        } elseif ($discountType === 'percentage' && $conditions['discount_value'] > 100) {
            $errors['conditions.discount_value'] = ['Percentage discount value must not exceed 100'];
        }

        // Validate apply_to_type
        $applyToType = $conditions['apply_to_type'] ?? null;
        if ($applyToType && !in_array($applyToType, ['all', 'specific_products', 'specific_categories'])) {
            $errors['conditions.apply_to_type'] = ['Apply to type must be: all, specific_products, or specific_categories'];
        }

        // Validate product/category IDs if specified
        if ($applyToType === 'specific_products') {
            if (empty($conditions['eligible_product_ids'])) {
                $errors['conditions.eligible_product_ids'] = ['Product IDs are required when apply_to_type is specific_products'];
            } elseif (!is_array($conditions['eligible_product_ids'])) {
                $errors['conditions.eligible_product_ids'] = ['Product IDs must be an array'];
            }
        }

        if ($applyToType === 'specific_categories') {
            if (empty($conditions['eligible_category_ids'])) {
                $errors['conditions.eligible_category_ids'] = ['Category IDs are required when apply_to_type is specific_categories'];
            } elseif (!is_array($conditions['eligible_category_ids'])) {
                $errors['conditions.eligible_category_ids'] = ['Category IDs must be an array'];
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Get validation message for Buy X Get Y Free
     */
    protected function getBuyXGetYFreeValidationMessage(array $conditions): string
    {
        if (!isset($conditions['buy_quantity'])) {
            return 'Buy quantity is required';
        }
        if (!isset($conditions['get_quantity'])) {
            return 'Get quantity is required';
        }
        return 'Invalid Buy X Get Y Free configuration';
    }

    /**
     * Get validation message for Step Discount
     */
    protected function getStepDiscountValidationMessage(array $conditions): string
    {
        if (!isset($conditions['discount_tiers'])) {
            return 'Discount tiers are required';
        }
        if (!is_array($conditions['discount_tiers']) || empty($conditions['discount_tiers'])) {
            return 'Discount tiers must be a non-empty array';
        }
        return 'Invalid Step Discount configuration';
    }

    /**
     * Get validation message for Fixed Price Bundle
     */
    protected function getFixedPriceBundleValidationMessage(array $conditions): string
    {
        if (!isset($conditions['bundle_quantity'])) {
            return 'Bundle quantity is required';
        }
        if (!isset($conditions['bundle_price'])) {
            return 'Bundle price is required';
        }
        return 'Invalid Fixed Price Bundle configuration';
    }

    /**
     * Get validation message for Percentage Discount
     */
    protected function getPercentageDiscountValidationMessage(array $conditions): string
    {
        if (!isset($conditions['discount_type'])) {
            return 'Discount type is required';
        }
        if (!isset($conditions['discount_value'])) {
            return 'Discount value is required';
        }
        return 'Invalid Percentage Discount configuration';
    }
}
