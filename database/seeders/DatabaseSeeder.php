<?php

namespace Database\Seeders;

use App\Enums\PromotionStatus;
use App\Enums\PromotionType;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Clothing', 'slug' => 'clothing'],
            ['name' => 'Skincare', 'slug' => 'skincare'],
            ['name' => 'Makeup', 'slug' => 'makeup'],
            ['name' => 'Food & Beverages', 'slug' => 'food-beverages'],
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'description' => "Category for {$categoryData['name']}",
                'sort_order' => 0,
            ]);
        }

        // Create products
        $products = [
            ['name' => 'Laptop', 'category' => 'Electronics', 'price' => 999.99, 'sku' => 'ELEC-001'],
            ['name' => 'Smartphone', 'category' => 'Electronics', 'price' => 699.99, 'sku' => 'ELEC-002'],
            ['name' => 'Headphones', 'category' => 'Electronics', 'price' => 149.99, 'sku' => 'ELEC-003'],
            ['name' => 'T-Shirt', 'category' => 'Clothing', 'price' => 19.99, 'sku' => 'CLO-001'],
            ['name' => 'Jeans', 'category' => 'Clothing', 'price' => 49.99, 'sku' => 'CLO-002'],
            ['name' => 'Sneakers', 'category' => 'Clothing', 'price' => 79.99, 'sku' => 'CLO-003'],
            ['name' => 'Face Cream', 'category' => 'Skincare', 'price' => 29.99, 'sku' => 'SKIN-001'],
            ['name' => 'Cleanser', 'category' => 'Skincare', 'price' => 19.99, 'sku' => 'SKIN-002'],
            ['name' => 'Serum', 'category' => 'Skincare', 'price' => 39.99, 'sku' => 'SKIN-003'],
            ['name' => 'Lipstick', 'category' => 'Makeup', 'price' => 15.99, 'sku' => 'MAKE-001'],
            ['name' => 'Foundation', 'category' => 'Makeup', 'price' => 34.99, 'sku' => 'MAKE-002'],
            ['name' => 'Mascara', 'category' => 'Makeup', 'price' => 12.99, 'sku' => 'MAKE-003'],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'sku' => $productData['sku'],
                'description' => "High quality {$productData['name']}",
                'category_id' => $category->id,
                'price' => $productData['price'],
                'cost' => $productData['price'] * 0.6,
                'stock_quantity' => rand(50, 200),
                'track_inventory' => true,
            ]);
        }

        // Get categories and products for promotions
        $clothingCategory = Category::where('name', 'Clothing')->first();
        $skincareCategory = Category::where('name', 'Skincare')->first();
        
        // 1. Buy X Get Y Free - Clothing
        Promotion::create([
            'code' => 'BUY2GET1',
            'name' => 'Buy 2 Get 1 Free - Clothing',
            'description' => 'Buy any 2 clothing items and get 1 free! The cheapest item will be free.',
            'type' => PromotionType::BUY_X_GET_Y_FREE->value,
            'status' => PromotionStatus::ACTIVE->value,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'conditions' => [
                'buy_quantity' => 2,
                'get_quantity' => 1,
                'apply_to_type' => 'specific_categories',
                'apply_to_category_ids' => $clothingCategory->id,
                'get_type' => 'cheapest',
                'apply_to_cheapest' => true,
            ],
            'benefits' => [],
        ]);

        // 2. Step Discount - All Products
        Promotion::create([
            'code' => 'STEP_DISCOUNT',
            'name' => 'Step Discount - 2nd 20%, 3rd 30%, 5th 50%',
            'description' => 'Get progressive discounts: 2nd item 20% off, 3rd item 30% off, 5th item 50% off',
            'type' => PromotionType::STEP_DISCOUNT->value,
            'status' => PromotionStatus::ACTIVE->value,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'conditions' => [
                'discount_tiers' => [
                    ['position' => 2, 'percentage' => 20],
                    ['position' => 3, 'percentage' => 30],
                    ['position' => 5, 'percentage' => 50],
                ],
            ],
            'benefits' => [],
        ]);

        // 3. Fixed Price Bundle - Skincare
        Promotion::create([
            'code' => 'SKINCARE_BUNDLE',
            'name' => 'Skincare Bundle - 3 for $30',
            'description' => 'Buy any 3 skincare products for just $30! Discount split proportionally.',
            'type' => PromotionType::FIXED_PRICE_BUNDLE->value,
            'status' => PromotionStatus::ACTIVE->value,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'conditions' => [
                'bundle_quantity' => 3,
                'bundle_price' => 30.00,
                'bundle_type' => 'specific_categories',
                'eligible_category_ids' => $skincareCategory->id,
            ],
            'benefits' => [],
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('User: test@example.com / password');
        $this->command->info('');
        $this->command->info('Sample Promotions Created:');
        $this->command->info('1. BUY2GET1 - Buy 2 Get 1 Free (Clothing)');
        $this->command->info('2. STEP_DISCOUNT - Progressive discounts by position');
        $this->command->info('3. SKINCARE_BUNDLE - 3 Skincare items for $30');
    }
}
