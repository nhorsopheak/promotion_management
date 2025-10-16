<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Promotion;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->active()
            ->orderBy('name')
            ->get();

        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        $customers = User::orderBy('name')->get();

        $promotions = Promotion::active()
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('products', 'categories', 'customers', 'promotions'));
    }

    public function getProducts(Request $request)
    {
        $query = Product::with('category')->active();

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('name')->get();

        return response()->json($products);
    }

    public function getCustomers(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->orderBy('name')->get();

        return response()->json($customers);
    }

    public function getPromotions(Request $request)
    {
        $promotions = Promotion::active()
            ->orderBy('name')
            ->get();

        return response()->json($promotions);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:users,id',
            'promotion_id' => 'nullable|exists:promotions,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'free_items' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Get customer info if customer_id provided
            $customerName = $request->customer_name;
            $customerEmail = $request->customer_email;
            $customerPhone = $request->customer_phone;

            if ($request->customer_id) {
                $customer = User::find($request->customer_id);
                if ($customer) {
                    $customerName = $customerName ?? $customer->name;
                    $customerEmail = $customerEmail ?? $customer->email;
                }
            }

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $request->customer_id,
                'subtotal' => $request->subtotal,
                'discount_amount' => $request->discount_amount ?? 0,
                'shipping_fee' => 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'total' => $request->total,
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => now(),
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'payment_method' => $request->payment_method ?? 'cash',
                'applied_promotions' => $request->promotion_id ? [
                    [
                        'promotion_id' => $request->promotion_id,
                        'discount_amount' => $request->discount_amount ?? 0,
                    ]
                ] : null,
            ]);

            // Create order items (regular items)
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'final_price' => $item['price'],
                    'discount_amount' => 0,
                    'subtotal' => $item['price'] * $item['quantity'],
                    'is_free' => false,
                ]);

                // Decrement stock
                $product->decrementStock($item['quantity']);
            }

            // Create order items for free items
            if ($request->free_items && is_array($request->free_items)) {
                foreach ($request->free_items as $freeItem) {
                    $product = Product::findOrFail($freeItem['product_id']);
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $freeItem['quantity'],
                        'price' => $freeItem['price'],
                        'final_price' => 0, // Free item
                        'discount_amount' => $freeItem['price'] * $freeItem['quantity'],
                        'subtotal' => 0,
                        'is_free' => true,
                        'promotion_id' => $request->promotion_id,
                    ]);

                    // Decrement stock for free items too
                    $product->decrementStock($freeItem['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order->load('items.product'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
