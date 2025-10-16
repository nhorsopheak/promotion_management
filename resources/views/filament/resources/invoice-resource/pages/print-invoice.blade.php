<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $record->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #111827;
            line-height: 1.6;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .invoice-title {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 5px;
        }

        .invoice-number {
            color: #6b7280;
            font-size: 14px;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .detail-section {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }

        .detail-title {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .free-section {
            background: #ecfdf5;
            border: 2px dashed #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .free-header {
            color: #059669;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .totals-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .grand-total {
            border-top: 2px solid #667eea;
            border-bottom: none;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 18px;
            font-weight: 700;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <h1 class="invoice-title">INVOICE</h1>
            <p class="invoice-number">Invoice #{{ $record->order_number }}</p>
            <p class="invoice-number">Date: {{ $record->created_at->format('F d, Y h:i A') }}</p>
        </div>

        <!-- Customer and Invoice Details -->
        <div class="invoice-details">
            <div class="detail-section">
                <h3 class="detail-title">Customer Information</h3>
                <div class="detail-row">
                    <span>Name:</span>
                    <span>{{ $record->customer_name ?: 'Walk-in Customer' }}</span>
                </div>
                @if($record->customer_email)
                <div class="detail-row">
                    <span>Email:</span>
                    <span>{{ $record->customer_email }}</span>
                </div>
                @endif
                @if($record->customer_phone)
                <div class="detail-row">
                    <span>Phone:</span>
                    <span>{{ $record->customer_phone }}</span>
                </div>
                @endif
            </div>

            <div class="detail-section">
                <h3 class="detail-title">Payment Information</h3>
                <div class="detail-row">
                    <span>Method:</span>
                    <span>{{ ucfirst($record->payment_method ?? 'Cash') }}</span>
                </div>
                <div class="detail-row">
                    <span>Status:</span>
                    <span>{{ ucfirst($record->payment_status) }}</span>
                </div>
                @if($record->paid_at)
                <div class="detail-row">
                    <span>Paid At:</span>
                    <span>{{ $record->paid_at->format('M d, Y h:i A') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Regular Items -->
        <h3 class="detail-title">Order Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->getRegularItems() as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name ?? $item->product->name }}</td>
                    <td>{{ $item->product_sku ?? $item->product->sku ?? 'N/A' }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Free Items -->
        @if($this->getFreeItems()->count() > 0)
        <div class="free-section">
            <h3 class="free-header">🎉 Free Items (Promotional)</h3>
            <table class="items-table" style="background: white;">
                <thead>
                    <tr style="background: #10b981;">
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getFreeItems() as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product_name ?? $item->product->name }}</td>
                        <td>{{ $item->product_sku ?? $item->product->sku ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-table">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>${{ number_format($record->subtotal, 2) }}</span>
                </div>
                @if($record->discount_amount > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span style="color: #10b981;">-${{ number_format($record->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="total-row">
                    <span>Tax:</span>
                    <span>${{ number_format($record->tax_amount, 2) }}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total:</span>
                    <span>${{ number_format($record->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice. No signature required.</p>
        </div>
    </div>
</body>
</html>
