<x-filament-panels::page>
    <style>
        @media print {
            .fi-header, .fi-sidebar, .fi-topbar, .fi-breadcrumbs, .no-print {
                display: none !important;
            }
            .fi-main {
                padding: 0 !important;
            }
            .invoice-container {
                box-shadow: none !important;
                border: none !important;
            }
        }
        
        .invoice-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .invoice-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }

        .invoice-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .invoice-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .detail-section {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .detail-title {
            font-weight: 700;
            color: #374151;
            margin-bottom: 1rem;
            font-size: 1.125rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .detail-value {
            color: #111827;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .items-section {
            margin: 2rem 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .items-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table tbody tr:hover {
            background: #f9fafb;
        }

        .items-table td {
            padding: 1rem;
            font-size: 0.875rem;
        }

        .product-name {
            font-weight: 600;
            color: #111827;
        }

        .product-sku {
            color: #6b7280;
            font-size: 0.75rem;
        }

        .free-items-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .free-items-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            color: #059669;
            margin-bottom: 1rem;
            font-size: 1.125rem;
        }

        .free-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .promotion-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .promotion-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 1rem;
            font-size: 1.125rem;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .totals-table {
            width: 400px;
            background: #f9fafb;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .total-row.grand-total {
            border-bottom: none;
            border-top: 2px solid #667eea;
            margin-top: 0.5rem;
            padding-top: 1rem;
        }

        .total-label {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .total-value {
            color: #111827;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .grand-total .total-label {
            color: #111827;
            font-size: 1.125rem;
            font-weight: 700;
        }

        .grand-total .total-value {
            color: #667eea;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .discount-value {
            color: #10b981;
        }

        .footer-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>

    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h1 class="invoice-title">INVOICE</h1>
                    <p class="invoice-subtitle">Invoice #{{ $record->order_number }}</p>
                </div>
                <div style="text-align: right;">
                    <div style="margin-bottom: 0.5rem;">
                        <span class="status-badge status-{{ $record->payment_status }}">
                            {{ strtoupper($record->payment_status) }}
                        </span>
                    </div>
                    <div>
                        <span class="status-badge status-{{ $record->status }}">
                            {{ strtoupper($record->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <!-- Customer Information -->
            <div class="detail-section">
                <h3 class="detail-title">
                    <span>👤</span> Customer Information
                </h3>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">{{ $record->customer_name ?: 'Walk-in Customer' }}</span>
                </div>
                @if($record->customer_email)
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $record->customer_email }}</span>
                </div>
                @endif
                @if($record->customer_phone)
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $record->customer_phone }}</span>
                </div>
                @endif
                @if($record->user)
                <div class="detail-row">
                    <span class="detail-label">Member:</span>
                    <span class="detail-value">
                        @if($record->user->is_member)
                            <span style="color: #10b981;">✓ Member</span>
                            @if($record->user->membership_tier)
                                ({{ $record->user->membership_tier }})
                            @endif
                        @else
                            Regular Customer
                        @endif
                    </span>
                </div>
                @endif
            </div>

            <!-- Invoice Information -->
            <div class="detail-section">
                <h3 class="detail-title">
                    <span>📄</span> Invoice Details
                </h3>
                <div class="detail-row">
                    <span class="detail-label">Invoice Date:</span>
                    <span class="detail-value">{{ $record->created_at->format('F d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span class="detail-value">{{ $record->created_at->format('h:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">{{ ucfirst($record->payment_method ?? 'Cash') }}</span>
                </div>
                @if($record->paid_at)
                <div class="detail-row">
                    <span class="detail-label">Paid At:</span>
                    <span class="detail-value">{{ $record->paid_at->format('F d, Y h:i A') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Promotion Section (if applicable) -->
        @if($this->hasPromotion())
        <div class="promotion-section">
            <div class="promotion-header">
                <span>🎁</span> Promotion Applied
            </div>
            @if($record->applied_promotions)
                @foreach($record->applied_promotions as $promo)
                <div class="detail-row">
                    <span class="detail-label">Promotion ID:</span>
                    <span class="detail-value">#{{ $promo['promotion_id'] ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Discount Amount:</span>
                    <span class="detail-value discount-value">${{ number_format($promo['discount_amount'] ?? 0, 2) }}</span>
                </div>
                @endforeach
            @endif
        </div>
        @endif

        <!-- Regular Items Section -->
        <div class="items-section">
            <h3 class="detail-title">
                <span>📦</span> Order Items
            </h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Product</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 10%; text-align: center;">Qty</th>
                        <th style="width: 15%; text-align: right;">Unit Price</th>
                        <th style="width: 20%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getRegularItems() as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="product-name">{{ $item->product_name ?? $item->product->name }}</div>
                            @if($item->product->category)
                            <div class="product-sku">Category: {{ $item->product->category->name }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="product-sku">{{ $item->product_sku ?? $item->product->sku ?? 'N/A' }}</span>
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                        <td style="text-align: right; font-weight: 600;">
                            ${{ number_format($item->subtotal ?? ($item->price * $item->quantity), 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Free Items Section (if applicable) -->
        @if($this->getFreeItems()->count() > 0)
        <div class="free-items-section">
            <div class="free-items-header">
                <span>🎉</span> Free Items (Promotional)
            </div>
            <table class="items-table" style="background: white; border-radius: 8px;">
                <thead style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 40%;">Product</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 10%; text-align: center;">Qty</th>
                        <th style="width: 15%; text-align: right;">Value</th>
                        <th style="width: 15%; text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getFreeItems() as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="product-name">{{ $item->product_name ?? $item->product->name }}</div>
                            @if($item->product->category)
                            <div class="product-sku">Category: {{ $item->product->category->name }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="product-sku">{{ $item->product_sku ?? $item->product->sku ?? 'N/A' }}</span>
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right; text-decoration: line-through; color: #6b7280;">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </td>
                        <td style="text-align: right;">
                            <span class="free-badge">FREE</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 1rem; padding: 0.75rem; background: white; border-radius: 8px;">
                <div class="detail-row">
                    <span class="detail-label">Total Free Items Value:</span>
                    <span class="detail-value" style="color: #10b981; font-size: 1.125rem;">
                        ${{ number_format($this->getFreeItems()->sum(function($item) { 
                            return $item->price * $item->quantity; 
                        }), 2) }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-table">
                <div class="total-row">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-value">${{ number_format($record->subtotal, 2) }}</span>
                </div>
                @if($record->discount_amount > 0)
                <div class="total-row">
                    <span class="total-label">Discount:</span>
                    <span class="total-value discount-value">-${{ number_format($record->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($record->shipping_fee > 0)
                <div class="total-row">
                    <span class="total-label">Shipping:</span>
                    <span class="total-value">${{ number_format($record->shipping_fee, 2) }}</span>
                </div>
                @endif
                <div class="total-row">
                    <span class="total-label">Tax (10%):</span>
                    <span class="total-value">${{ number_format($record->tax_amount, 2) }}</span>
                </div>
                <div class="total-row grand-total">
                    <span class="total-label">Total Amount:</span>
                    <span class="total-value">${{ number_format($record->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div style="margin-top: 2rem; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
            <h3 class="detail-title">
                <span>📊</span> Order Summary
            </h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 1rem;">
                <div style="text-align: center; padding: 1rem; background: white; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">
                        {{ $record->items->sum('quantity') }}
                    </div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Total Items</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: white; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">
                        {{ $this->getFreeItems()->sum('quantity') }}
                    </div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Free Items</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: white; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                        ${{ number_format($record->discount_amount, 2) }}
                    </div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Total Saved</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: white; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">
                        ${{ number_format($record->total, 2) }}
                    </div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Final Total</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <p style="margin-bottom: 0.5rem;">Thank you for your business!</p>
            <p style="font-size: 0.75rem;">This is a computer-generated invoice. No signature required.</p>
            <p style="font-size: 0.75rem; margin-top: 1rem;">
                Generated on {{ now()->format('F d, Y h:i A') }}
            </p>
        </div>
    </div>
</x-filament-panels::page>
