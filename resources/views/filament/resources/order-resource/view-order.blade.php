<x-filament-panels::page
    {{ $applyStateBindingModifiers('wire:loading') }}
>
    <div class="space-y-6">
        <!-- Order Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Order #{{ $record->order_number }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Created {{ $record->created_at->format('M j, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ match($record->status) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                        {{ ucfirst($record->status) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ match($record->payment_status) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-gray-100 text-gray-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                        {{ ucfirst($record->payment_status) }}
                    </span>
                </div>
            </div>

            <!-- Customer Info -->
            @if($record->customer_name || $record->user)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold mb-2">Customer</h3>
                        @if($record->user)
                            <p class="text-sm">{{ $record->user->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->user->email }}</p>
                        @else
                            <p class="text-sm">{{ $record->customer_name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->customer_email }}</p>
                        @endif
                    </div>
                    @if($record->customer_phone)
                        <div>
                            <h3 class="font-semibold mb-2">Contact</h3>
                            <p class="text-sm">{{ $record->customer_phone }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Order Items -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Order Items</h3>

                <div class="space-y-4">
                    @foreach($record->items as $item)
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center space-x-4">
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                @endif

                                <div>
                                    <p class="font-medium">{{ $item->product_name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->product_sku }}</p>
                                    @if($item->is_free)
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full mt-1">FREE ITEM</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="font-medium">
                                    {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                </p>
                                @if($item->discount_amount > 0)
                                    <p class="text-sm text-green-600">
                                        - ${{ number_format($item->discount_amount, 2) }}
                                    </p>
                                @endif
                                <p class="text-lg font-bold">
                                    ${{ number_format($item->subtotal, 2) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>${{ number_format($record->subtotal, 2) }}</span>
                        </div>
                        @if($record->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-${{ number_format($record->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        @if($record->shipping_fee > 0)
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span>${{ number_format($record->shipping_fee, 2) }}</span>
                            </div>
                        @endif
                        @if($record->tax_amount > 0)
                            <div class="flex justify-between">
                                <span>Tax</span>
                                <span>${{ number_format($record->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-2 border-t">
                            <span>Total</span>
                            <span>${{ number_format($record->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applied Promotions -->
        @if($record->applied_promotions && !empty($record->applied_promotions))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Applied Promotions</h3>

                    <div class="space-y-3">
                        @foreach($record->applied_promotions as $promotion)
                            @if($promotion['applied'] ?? false)
                                <div class="p-4 border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-green-800 dark:text-green-400">
                                                {{ $promotion['promotion_name'] }}
                                            </h4>
                                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                                {{ $promotion['message'] }}
                                            </p>
                                            @if(isset($promotion['metadata']))
                                                <div class="mt-2 text-xs text-green-600 dark:text-green-500">
                                                    @if(isset($promotion['metadata']['buy_quantity']))
                                                        Buy {{ $promotion['metadata']['buy_quantity'] }} Get {{ $promotion['metadata']['get_quantity'] }} Free
                                                    @endif
                                                    @if(isset($promotion['metadata']['sets_qualified']))
                                                        ({{ $promotion['metadata']['sets_qualified'] }} sets qualified)
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @if($promotion['discount_amount'] > 0)
                                            <span class="text-lg font-bold text-green-600">
                                                -${{ number_format($promotion['discount_amount'], 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Promotion Logs -->
        @if($record->promotionLogs->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Promotion Activity</h3>

                    <div class="space-y-3">
                        @foreach($record->promotionLogs as $log)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div>
                                    <p class="font-medium">{{ $log->promotion->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ ucfirst($log->action) }} at {{ $log->created_at->format('M j, g:i A') }}
                                    </p>
                                    @if($log->discount_amount > 0)
                                        <p class="text-sm text-green-600">Discount: ${{ number_format($log->discount_amount, 2) }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->action === 'applied' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
