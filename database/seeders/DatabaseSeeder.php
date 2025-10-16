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
        // Create admin user directly (avoid factory issues)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create test user directly
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create medicine categories
        $categories = [
            ['name' => 'Pain Relief', 'slug' => 'pain-relief'],
            ['name' => 'Cold & Flu', 'slug' => 'cold-flu'],
            ['name' => 'Vitamins & Supplements', 'slug' => 'vitamins-supplements'],
            ['name' => 'Digestive Health', 'slug' => 'digestive-health'],
            ['name' => 'First Aid', 'slug' => 'first-aid'],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'description' => "Category for {$categoryData['name']}",
                    'sort_order' => 0,
                ]
            );
        }

        // Create medicine products
        $products = [
            ['name' => 'Paracetamol 500mg', 'category' => 'Pain Relief', 'price' => 5.99, 'sku' => 'PAIN-001'],
            ['name' => 'Ibuprofen 400mg', 'category' => 'Pain Relief', 'price' => 7.99, 'sku' => 'PAIN-002'],
            ['name' => 'Aspirin 100mg', 'category' => 'Pain Relief', 'price' => 6.49, 'sku' => 'PAIN-003'],
            ['name' => 'Cold & Flu Relief Tablets', 'category' => 'Cold & Flu', 'price' => 8.99, 'sku' => 'COLD-001'],
            ['name' => 'Cough Syrup 200ml', 'category' => 'Cold & Flu', 'price' => 12.99, 'sku' => 'COLD-002'],
            ['name' => 'Throat Lozenges', 'category' => 'Cold & Flu', 'price' => 4.99, 'sku' => 'COLD-003'],
            ['name' => 'Vitamin C 1000mg', 'category' => 'Vitamins & Supplements', 'price' => 15.99, 'sku' => 'VIT-001'],
            ['name' => 'Multivitamin Complex', 'category' => 'Vitamins & Supplements', 'price' => 24.99, 'sku' => 'VIT-002'],
            ['name' => 'Omega-3 Fish Oil', 'category' => 'Vitamins & Supplements', 'price' => 19.99, 'sku' => 'VIT-003'],
            ['name' => 'Antacid Tablets', 'category' => 'Digestive Health', 'price' => 6.99, 'sku' => 'DIG-001'],
            ['name' => 'Probiotic Capsules', 'category' => 'Digestive Health', 'price' => 22.99, 'sku' => 'DIG-002'],
            ['name' => 'Laxative Syrup', 'category' => 'Digestive Health', 'price' => 9.99, 'sku' => 'DIG-003'],
            ['name' => 'Adhesive Bandages (Pack of 50)', 'category' => 'First Aid', 'price' => 3.99, 'sku' => 'AID-001'],
            ['name' => 'Antiseptic Cream 30g', 'category' => 'First Aid', 'price' => 5.49, 'sku' => 'AID-002'],
            ['name' => 'Gauze Pads (Pack of 10)', 'category' => 'First Aid', 'price' => 4.49, 'sku' => 'AID-003'],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => "High quality {$productData['name']}",
                    'category_id' => $category->id,
                    'price' => $productData['price'],
                    'cost' => $productData['price'] * 0.6,
                    'stock_quantity' => rand(50, 200),
                    'track_inventory' => true,
                ]
            );
        }

        // Get categories for promotions
        $painReliefCategory = Category::where('name', 'Pain Relief')->first();
        $vitaminsCategory = Category::where('name', 'Vitamins & Supplements')->first();
        
        // 1. Buy X Get Y Free - Pain Relief
        Promotion::updateOrCreate(
            ['code' => 'BUY2GET1'],
            [
                'name' => 'Buy 2 Get 1 Free - Pain Relief',
                'description' => 'Buy any 2 pain relief medicines and get 1 free! The cheapest item will be free.',
                'type' => PromotionType::BUY_X_GET_Y_FREE->value,
                'status' => PromotionStatus::ACTIVE->value,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'conditions' => [
                    'buy_quantity' => 2,
                    'get_quantity' => 1,
                    'apply_to_type' => 'specific_categories',
                    'apply_to_category_ids' => $painReliefCategory->id,
                    'get_type' => 'cheapest',
                    'apply_to_cheapest' => true,
                ],
                'benefits' => [],
            ]
        );

        // 2. Step Discount - All Products
        Promotion::updateOrCreate(
            ['code' => 'STEP_DISCOUNT'],
            [
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
            ]
        );

        // 3. Fixed Price Bundle - Vitamins
        Promotion::updateOrCreate(
            ['code' => 'VITAMIN_BUNDLE'],
            [
                'name' => 'Vitamin Bundle - 3 for $50',
                'description' => 'Buy any 3 vitamins & supplements for just $50! Discount split proportionally.',
                'type' => PromotionType::FIXED_PRICE_BUNDLE->value,
                'status' => PromotionStatus::ACTIVE->value,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'conditions' => [
                    'bundle_quantity' => 3,
                    'bundle_price' => 50.00,
                    'bundle_type' => 'specific_categories',
                    'eligible_category_ids' => $vitaminsCategory->id,
                ],
                'benefits' => [],
            ]
        );

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('User: test@example.com / password');
        $this->command->info('');
        $this->command->info('Sample Promotions Created:');
        $this->command->info('1. BUY2GET1 - Buy 2 Get 1 Free (Pain Relief)');
        $this->command->info('2. STEP_DISCOUNT - Progressive discounts by position');
        $this->command->info('3. VITAMIN_BUNDLE - 3 Vitamins for $50');
    }
}
