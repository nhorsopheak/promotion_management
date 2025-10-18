<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            height: 100vh;
            overflow: hidden;
        }

        .pos-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Top Navigation Bar */
        .top-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .top-nav h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-menu {
            display: flex;
            gap: 1rem;
        }

        .nav-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        /* Control Bar */
        .control-bar {
            background: white;
            padding: 1rem 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .control-group {
            flex: 1;
        }

        .control-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .control-group select,
        .control-group input {
            width: 100%;
            padding: 0.625rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.3s;
        }

        .control-group select:focus,
        .control-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Main Content */
        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Products Section - Left */
        .products-section {
            flex: 2;
            background: white;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e5e7eb;
        }

        .products-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.875rem;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .category-tabs {
            display: flex;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            overflow-x: auto;
            border-bottom: 1px solid #e5e7eb;
        }

        .category-tab {
            padding: 0.5rem 1rem;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            white-space: nowrap;
            transition: all 0.3s;
        }

        .category-tab:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .category-tab.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .products-grid {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
            align-content: start;
        }

        .product-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .product-card:hover {
            border-color: #667eea;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }

        .product-image {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .product-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .product-price {
            color: #667eea;
            font-weight: 700;
            font-size: 1rem;
        }

        .product-stock {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        /* Cart Section - Right */
        .cart-section {
            flex: 1;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            min-width: 400px;
        }

        .cart-header {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .cart-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }

        .cart-item-name {
            font-weight: 600;
            color: #111827;
            font-size: 0.875rem;
        }

        .cart-item-remove {
            background: #fee;
            color: #dc2626;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s;
        }

        .cart-item-remove:hover {
            background: #dc2626;
            color: white;
        }

        .cart-item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qty-btn {
            background: #667eea;
            color: white;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: #5568d3;
        }

        .qty-display {
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }

        .cart-item-price {
            font-weight: 700;
            color: #667eea;
            font-size: 1rem;
        }

        .cart-item-price.free {
            color: #10b981;
            text-decoration: line-through;
        }

        .original-price {
            text-decoration: line-through !important;
            color: #9ca3af !important;
            font-size: 0.875rem !important;
            font-weight: 400 !important;
        }

        .discounted-price {
            color: #10b981 !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
        }

        .free-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-left: 0.5rem;
        }

        .free-items-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px dashed #10b981;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .free-items-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            color: #059669;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }

        .free-item {
            background: white;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .free-item:last-child {
            margin-bottom: 0;
        }

        .free-item-name {
            font-weight: 600;
            color: #111827;
            font-size: 0.875rem;
        }

        .free-item-qty {
            color: #059669;
            font-weight: 700;
        }

        .promotion-applied {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .promotion-applied-text {
            font-size: 0.875rem;
            color: #92400e;
            font-weight: 600;
        }

        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-cart-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        /* Cart Summary */
        .cart-summary {
            background: white;
            padding: 1.5rem;
            border-top: 2px solid #e5e7eb;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }

        .summary-row.total {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            padding-top: 0.75rem;
            border-top: 2px solid #e5e7eb;
            margin-top: 0.75rem;
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.3s;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .checkout-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="pos-container">
        <!-- Top Navigation -->
        <div class="top-nav">
            <h1>🛒 POS System</h1>
            <div class="nav-menu">
                <a href="/admin/invoices" class="nav-btn" style="background: rgba(255,255,255,0.3); font-weight: 600; text-decoration: none; display: flex; align-items: center;" onclick="console.log('Navigating to invoices...'); return true;">
                    ← Back to Invoices
                </a>
                <a href="/admin" class="nav-btn" style="text-decoration: none; display: flex; align-items: center;">📊 Dashboard</a>
                <a href="/admin/orders" class="nav-btn" style="text-decoration: none; display: flex; align-items: center;">📦 Orders</a>
                <a href="/admin/invoices" class="nav-btn" style="text-decoration: none; display: flex; align-items: center;">📄 Invoices</a>
            </div>
        </div>

        <!-- Control Bar -->
        <div class="control-bar">
            <div class="control-group">
                <label for="customer-select">Customer</label>
                <select id="customer-select">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-email="{{ $customer->email }}">
                            {{ $customer->name }} @if($customer->is_member)(Member)@endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="control-group">
                <label for="promotion-select">Promotion</label>
                <select id="promotion-select">
                    <option value="">No Promotion</option>
                    @foreach($promotions as $promotion)
                        <option value="{{ $promotion->id }}" data-promotion="{{ json_encode($promotion) }}">
                            {{ $promotion->name }} ({{ $promotion->type }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Products Section -->
            <div class="products-section">
                <div class="products-header">
                    <div class="search-box">
                        <span class="search-icon">🔍</span>
                        <input type="text" id="product-search" placeholder="Search products...">
                    </div>
                </div>

                <div class="category-tabs">
                    <button class="category-tab active" data-category="">All Products</button>
                    @foreach($categories as $category)
                        <button class="category-tab" data-category="{{ $category->id }}">
                            {{ $category->name }} ({{ $category->products_count }})
                        </button>
                    @endforeach
                </div>

                <div class="products-grid" id="products-grid">
                    @foreach($products as $product)
                        <div class="product-card" 
                             data-product-id="{{ $product->id }}"
                             data-product-name="{{ $product->name }}"
                             data-product-price="{{ $product->price }}"
                             data-product-stock="{{ $product->stock_quantity }}"
                             data-category-id="{{ $product->category_id }}">
                            <div class="product-image">
                                {{ strtoupper(substr($product->name, 0, 1)) }}
                            </div>
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-price">${{ number_format($product->price, 2) }}</div>
                            @if($product->track_inventory)
                                <div class="product-stock">Stock: {{ $product->stock_quantity }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Cart Section -->
            <div class="cart-section">
                <div class="cart-header">
                    <h2>Current Order</h2>
                </div>

                <div class="cart-items" id="cart-items">
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <p>Cart is empty</p>
                        <p style="font-size: 0.875rem; margin-top: 0.5rem;">Add products to start</p>
                    </div>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Discount:</span>
                        <span id="discount">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span id="tax">$0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">$0.00</span>
                    </div>
                    <button class="checkout-btn" id="checkout-btn" disabled>
                        💳 Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cart state
        let cart = [];
        let selectedCustomer = null;
        let selectedPromotion = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeEventListeners();
        });

        function initializeEventListeners() {
            // Product cards
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function() {
                    addToCart({
                        id: this.dataset.productId,
                        name: this.dataset.productName,
                        price: parseFloat(this.dataset.productPrice),
                        stock: parseInt(this.dataset.productStock)
                    });
                });
            });

            // Category tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    filterProducts(this.dataset.category);
                });
            });

            // Search
            document.getElementById('product-search').addEventListener('input', function(e) {
                searchProducts(e.target.value);
            });

            // Customer select
            document.getElementById('customer-select').addEventListener('change', function(e) {
                const option = e.target.options[e.target.selectedIndex];
                selectedCustomer = e.target.value ? {
                    id: e.target.value,
                    name: option.dataset.name,
                    email: option.dataset.email
                } : null;
            });

            // Promotion select
            document.getElementById('promotion-select').addEventListener('change', function(e) {
                selectedPromotion = e.target.value ? JSON.parse(e.target.options[e.target.selectedIndex].dataset.promotion) : null;
                console.log('Promotion selected:', selectedPromotion);
                updateCart();
            });

            // Checkout button
            document.getElementById('checkout-btn').addEventListener('click', checkout);
        }

        function addToCart(product) {
            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    stock: product.stock
                });
            }
            
            updateCart();
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCart();
        }

        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    updateCart();
                }
            }
        }

        function calculatePromotionBenefits() {
            if (!selectedPromotion) return { freeItems: [], discount: 0 };

            const freeItems = [];
            let discount = 0;

            // Handle Buy X Get Y Free promotions
            if (selectedPromotion.type === 'buy_x_get_y_free') {
                const conditions = selectedPromotion.conditions || {};
                const benefits = selectedPromotion.benefits || {};
                const buyQuantity = conditions.min_quantity || 2;
                const freeQuantity = benefits.free_quantity || 1;

                cart.forEach(item => {
                    const eligibleSets = Math.floor(item.quantity / buyQuantity);
                    if (eligibleSets > 0) {
                        const totalFree = eligibleSets * freeQuantity;
                        freeItems.push({
                            id: item.id,
                            name: item.name,
                            quantity: totalFree,
                            price: item.price
                        });
                        discount += totalFree * item.price;
                    }
                });
            }

            // Handle percentage discount
            if (selectedPromotion.type === 'percentage_discount') {
                const benefits = selectedPromotion.benefits || {};
                const percentage = benefits.discount_percentage || 0;
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                discount = subtotal * (percentage / 100);
            }

            // Handle fixed amount discount
            if (selectedPromotion.type === 'fixed_amount_discount') {
                const benefits = selectedPromotion.benefits || {};
                discount = benefits.discount_amount || 0;
            }

            return { freeItems, discount };
        }

        function updateCart() {
            const cartItemsContainer = document.getElementById('cart-items');
            
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <p>Cart is empty</p>
                        <p style="font-size: 0.875rem; margin-top: 0.5rem;">Add products to start</p>
                    </div>
                `;
                document.getElementById('checkout-btn').disabled = true;
            } else {
                const { freeItems, discount } = calculatePromotionBenefits();
                
                let cartHTML = '';

                // Show promotion applied banner
                if (selectedPromotion) {
                    cartHTML += `
                        <div class="promotion-applied">
                            <span>🎁</span>
                            <span class="promotion-applied-text">${selectedPromotion.name} Applied!</span>
                        </div>
                    `;
                }

                // Show regular cart items
                cartHTML += cart.map(item => {
                    const originalTotal = item.price * item.quantity;
                    let discountedPrice = item.price;
                    let hasDiscount = false;
                    
                    // Check if this item has a discount from promotion
                    if (selectedPromotion && selectedPromotion.type === 'percentage_discount') {
                        const benefits = selectedPromotion.benefits || {};
                        const percentage = benefits.discount_percentage || 0;
                        discountedPrice = item.price * (1 - percentage / 100);
                        hasDiscount = percentage > 0;
                        console.log(`Percentage discount: ${percentage}%, original: $${item.price}, discounted: $${discountedPrice}`);
                    } else if (selectedPromotion && selectedPromotion.type === 'fixed_amount_discount') {
                        const benefits = selectedPromotion.benefits || {};
                        const fixedDiscount = benefits.discount_amount || 0;
                        const totalItems = cart.reduce((sum, cartItem) => sum + cartItem.quantity, 0);
                        const discountPerItem = fixedDiscount / totalItems;
                        discountedPrice = Math.max(0, item.price - discountPerItem);
                        hasDiscount = fixedDiscount > 0;
                        console.log(`Fixed discount: $${fixedDiscount}, per item: $${discountPerItem}, original: $${item.price}, discounted: $${discountedPrice}`);
                    } else if (selectedPromotion && selectedPromotion.type === 'step_discount') {
                        const conditions = selectedPromotion.conditions || {};
                        const discountTiers = conditions.discount_tiers || {};
                        
                        // Calculate discount based on quantity tiers
                        let applicableDiscount = 0;
                        for (const [minQty, discountPercent] of Object.entries(discountTiers)) {
                            if (item.quantity >= parseInt(minQty)) {
                                applicableDiscount = Math.max(applicableDiscount, parseFloat(discountPercent));
                            }
                        }
                        
                        if (applicableDiscount > 0) {
                            discountedPrice = item.price * (1 - applicableDiscount / 100);
                            hasDiscount = true;
                            console.log(`Step discount: ${applicableDiscount}% for qty ${item.quantity}, original: $${item.price}, discounted: $${discountedPrice}`);
                        }
                    }
                    
                    const discountedTotal = discountedPrice * item.quantity;
                    console.log(`Item: ${item.name}, hasDiscount: ${hasDiscount}, original: $${originalTotal}, discounted: $${discountedTotal}`);
                    
                    return `
                        <div class="cart-item">
                            <div class="cart-item-header">
                                <div class="cart-item-name">${item.name}</div>
                                <button class="cart-item-remove" onclick="removeFromCart('${item.id}')">×</button>
                            </div>
                            <div class="cart-item-controls">
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="updateQuantity('${item.id}', -1)">−</button>
                                    <span class="qty-display">${item.quantity}</span>
                                    <button class="qty-btn" onclick="updateQuantity('${item.id}', 1)">+</button>
                                </div>
                                <div class="cart-item-price">
                                    ${hasDiscount ? `
                                        <div class="original-price">
                                            $${originalTotal.toFixed(2)}
                                        </div>
                                        <div class="discounted-price">
                                            $${discountedTotal.toFixed(2)}
                                        </div>
                                    ` : `$${originalTotal.toFixed(2)}`}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                // Show free items section
                if (freeItems.length > 0) {
                    cartHTML += `
                        <div class="free-items-section">
                            <div class="free-items-header">
                                <span>🎉</span>
                                <span>Free Items</span>
                            </div>
                            ${freeItems.map(item => `
                                <div class="free-item">
                                    <div>
                                        <div class="free-item-name">${item.name}</div>
                                        <span class="free-badge">FREE</span>
                                    </div>
                                    <div class="free-item-qty">×${item.quantity}</div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                }

                cartItemsContainer.innerHTML = cartHTML;
                document.getElementById('checkout-btn').disabled = false;

                // Calculate totals with proper discount handling
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                
                // Calculate actual discounted amount for display
                let actualDiscountedSubtotal = subtotal;
                if (selectedPromotion && selectedPromotion.type === 'percentage_discount') {
                    const benefits = selectedPromotion.benefits || {};
                    const percentage = benefits.discount_percentage || 0;
                    actualDiscountedSubtotal = subtotal * (1 - percentage / 100);
                } else if (selectedPromotion && selectedPromotion.type === 'fixed_amount_discount') {
                    const benefits = selectedPromotion.benefits || {};
                    const fixedDiscount = benefits.discount_amount || 0;
                    actualDiscountedSubtotal = Math.max(0, subtotal - fixedDiscount);
                } else if (selectedPromotion && selectedPromotion.type === 'step_discount') {
                    // Calculate step discount total
                    actualDiscountedSubtotal = 0;
                    cart.forEach(item => {
                        const conditions = selectedPromotion.conditions || {};
                        const discountTiers = conditions.discount_tiers || {};
                        
                        let applicableDiscount = 0;
                        for (const [minQty, discountPercent] of Object.entries(discountTiers)) {
                            if (item.quantity >= parseInt(minQty)) {
                                applicableDiscount = Math.max(applicableDiscount, parseFloat(discountPercent));
                            }
                        }
                        
                        const discountedPrice = applicableDiscount > 0 ? 
                            item.price * (1 - applicableDiscount / 100) : item.price;
                        actualDiscountedSubtotal += discountedPrice * item.quantity;
                    });
                } else {
                    // For buy_x_get_y_free, use the original discount calculation
                    actualDiscountedSubtotal = subtotal - discount;
                }
                
                const finalDiscount = subtotal - actualDiscountedSubtotal;
                const tax = actualDiscountedSubtotal * 0.1; // 10% tax on discounted amount
                const total = actualDiscountedSubtotal + tax;

                document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
                document.getElementById('discount').textContent = `$${finalDiscount.toFixed(2)}`;
                document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
                document.getElementById('total').textContent = `$${total.toFixed(2)}`;
            }
        }

        function filterProducts(categoryId) {
            const products = document.querySelectorAll('.product-card');
            products.forEach(product => {
                if (!categoryId || product.dataset.categoryId === categoryId) {
                    product.style.display = 'flex';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        function searchProducts(query) {
            const products = document.querySelectorAll('.product-card');
            const searchTerm = query.toLowerCase();
            
            products.forEach(product => {
                const name = product.dataset.productName.toLowerCase();
                if (name.includes(searchTerm)) {
                    product.style.display = 'flex';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        async function checkout() {
            if (cart.length === 0) return;

            const checkoutBtn = document.getElementById('checkout-btn');
            checkoutBtn.disabled = true;
            checkoutBtn.textContent = '⏳ Processing...';

            try {
                // Calculate totals and free items using the same logic as display
                const { freeItems, discount } = calculatePromotionBenefits();
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                
                // Calculate actual discounted amount (same as display logic)
                let actualDiscountedSubtotal = subtotal;
                if (selectedPromotion && selectedPromotion.type === 'percentage_discount') {
                    const benefits = selectedPromotion.benefits || {};
                    const percentage = benefits.discount_percentage || 0;
                    actualDiscountedSubtotal = subtotal * (1 - percentage / 100);
                } else if (selectedPromotion && selectedPromotion.type === 'fixed_amount_discount') {
                    const benefits = selectedPromotion.benefits || {};
                    const fixedDiscount = benefits.discount_amount || 0;
                    actualDiscountedSubtotal = Math.max(0, subtotal - fixedDiscount);
                } else if (selectedPromotion && selectedPromotion.type === 'step_discount') {
                    // Calculate step discount total (same logic as display)
                    actualDiscountedSubtotal = 0;
                    cart.forEach(item => {
                        const conditions = selectedPromotion.conditions || {};
                        const discountTiers = conditions.discount_tiers || {};
                        
                        let applicableDiscount = 0;
                        for (const [minQty, discountPercent] of Object.entries(discountTiers)) {
                            if (item.quantity >= parseInt(minQty)) {
                                applicableDiscount = Math.max(applicableDiscount, parseFloat(discountPercent));
                            }
                        }
                        
                        const discountedPrice = applicableDiscount > 0 ? 
                            item.price * (1 - applicableDiscount / 100) : item.price;
                        actualDiscountedSubtotal += discountedPrice * item.quantity;
                    });
                } else {
                    // For buy_x_get_y_free, use the original discount calculation
                    actualDiscountedSubtotal = subtotal - discount;
                }
                
                const finalDiscount = subtotal - actualDiscountedSubtotal;
                const taxAmount = actualDiscountedSubtotal * 0.1;
                const total = actualDiscountedSubtotal + taxAmount;

                const response = await fetch('/pos/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        items: cart.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            price: item.price
                        })),
                        free_items: freeItems.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            price: item.price
                        })),
                        customer_id: selectedCustomer?.id,
                        customer_name: selectedCustomer?.name,
                        customer_email: selectedCustomer?.email,
                        promotion_id: selectedPromotion?.id,
                        discount_amount: finalDiscount,
                        tax_amount: taxAmount,
                        subtotal: subtotal,
                        total: total,
                        payment_method: 'cash'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(`✅ Order ${data.order.order_number} created successfully!\n\nSubtotal: $${subtotal.toFixed(2)}\nDiscount: $${finalDiscount.toFixed(2)}\nTax: $${taxAmount.toFixed(2)}\nTotal: $${total.toFixed(2)}`);
                    // Reset cart
                    cart = [];
                    selectedCustomer = null;
                    selectedPromotion = null;
                    document.getElementById('customer-select').value = '';
                    document.getElementById('promotion-select').value = '';
                    updateCart();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Failed to process checkout: ' + error.message);
            } finally {
                checkoutBtn.disabled = false;
                checkoutBtn.textContent = '💳 Checkout';
            }
        }
    </script>
</body>
</html>
