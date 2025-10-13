<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Console\Command;

class CreateSampleOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sample:order {--customer=Walk-in Customer : Customer name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a sample order to demonstrate Buy X Get Y Free promotion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customerName = $this->option('customer');

        $this->info("Creating sample order for: {$customerName}");
        $this->info("Adding 3 clothing items to demonstrate Buy 2 Get 1 Free promotion...");

        // Get products
        $tshirt = Product::where('sku', 'CLO-001')->first();
        $jeans = Product::where('sku', 'CLO-002')->first();
        $sneakers = Product::where('sku', 'CLO-003')->first();

        if (!$tshirt || !$jeans || !$sneakers) {
            $this->error('Required products not found. Run php artisan db:seed first.');
            return 1;
        }

        // Use cart service to simulate adding products
        $cartService = app(CartService::class);

        // Add products to cart
        $cartService->addItem($tshirt, 1);
        $cartService->addItem($jeans, 1);
        $cartService->addItem($sneakers, 1);

        // Get cart with promotions applied
        $cartData = $cartService->getCartWithPromotions();

        $this->info("Cart Summary:");
        $this->line("├── Subtotal: $" . number_format($cartData['subtotal'], 2));
        $this->line("├── Discount: $" . number_format($cartData['discount'], 2));
        $this->line("└── Total: $" . number_format($cartData['total'], 2));

        if (!empty($cartData['promotions'])) {
            $this->info("\nApplied Promotions:");
            foreach ($cartData['promotions'] as $promotion) {
                if ($promotion['applied']) {
                    $this->line("├── {$promotion['promotion_name']}");
                    $this->line("│   ├── {$promotion['message']}");
                    $this->line("│   └── Saved: $" . number_format($promotion['discount_amount'], 2));
                }
            }
        }

        // Create the order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => $customerName,
            'subtotal' => $cartData['subtotal'],
            'discount_amount' => $cartData['discount'],
            'shipping_fee' => 0,
            'tax_amount' => 0,
            'total' => $cartData['total'],
            'status' => 'completed',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'applied_promotions' => $cartData['promotions'],
        ]);

        // Create order items
        foreach ($cartData['items'] as $itemData) {
            $order->items()->create([
                'product_id' => $itemData['product_id'],
                'product_name' => $itemData['product_name'],
                'product_sku' => $itemData['product_sku'],
                'price' => $itemData['price'],
                'discount_amount' => $itemData['discount_amount'],
                'final_price' => $itemData['final_price'],
                'quantity' => $itemData['quantity'],
                'subtotal' => $itemData['subtotal'],
                'is_free' => $itemData['is_free'],
                'promotion_id' => $itemData['promotion_id'],
                'promotion_details' => $itemData['promotion_details'],
            ]);
        }

        // Log promotion usage
        foreach ($cartData['promotions'] as $promotionData) {
            if ($promotionData['applied']) {
                $promotion = \App\Models\Promotion::find($promotionData['promotion_id']);
                if ($promotion) {
                    $promotion->logs()->create([
                        'order_id' => $order->id,
                        'action' => 'applied',
                        'discount_amount' => $promotionData['discount_amount'],
                        'affected_items' => $promotionData['affected_items'],
                        'metadata' => $promotionData,
                    ]);
                }
            }
        }

        // Clear cart
        $cartService->clear();

        $this->info("\n✅ Sample order created successfully!");
        $this->info("Order #{$order->order_number}");
        $this->info("Total: $" . number_format($order->total, 2));
        $this->info("Saved: $" . number_format($order->discount_amount, 2));

        $this->comment("\n📊 You can now view this order in the admin panel:");
        $this->comment("1. Go to http://localhost:8000/admin");
        $this->comment("2. Navigate to Sales > Orders");
        $this->comment("3. Click on Order #{$order->order_number} to see details");

        return 0;
    }
}
