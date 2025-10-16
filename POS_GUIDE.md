# POS System Guide

## Overview
A modern, intuitive Point of Sale (POS) system for managing sales transactions with customer and promotion management.

## Features

### 🎨 Modern UI
- **Gradient design** with purple theme
- **Responsive layout** optimized for desktop and tablet
- **Real-time updates** for cart and totals
- **Smooth animations** and transitions

### 🛒 Product Management
- **Product grid** with search functionality
- **Category filtering** for easy navigation
- **Stock tracking** display
- **Quick add to cart** with single click

### 👥 Customer Management
- **Customer selection** dropdown
- **Walk-in customer** option
- **Member identification** with badges
- **Customer details** stored with orders

### 🎁 Promotion System
- **Promotion selection** dropdown
- **Date-based filtering** (only active promotions shown)
- **Multiple promotion types** support
- **Automatic discount** calculation

### 💳 Checkout Process
- **Cart management** with quantity controls
- **Real-time totals** calculation
- **Tax calculation** (10% default)
- **Order creation** with unique order numbers

## Layout Structure

```
┌─────────────────────────────────────────────────────────┐
│  🛒 POS System        📊 Dashboard  📦 Orders  ⚙️ Settings │
├─────────────────────────────────────────────────────────┤
│  Customer: [Select]     Promotion: [Select]             │
├──────────────────────────┬──────────────────────────────┤
│                          │                              │
│  Products Section        │  Cart Section                │
│  ┌──────────────────┐   │  ┌────────────────────────┐  │
│  │ 🔍 Search        │   │  │ Current Order          │  │
│  └──────────────────┘   │  └────────────────────────┘  │
│                          │                              │
│  [All] [Category 1]...   │  Cart Items:                 │
│                          │  - Product 1  [- 2 +] $20    │
│  ┌───┐ ┌───┐ ┌───┐      │  - Product 2  [- 1 +] $15    │
│  │ A │ │ B │ │ C │      │                              │
│  └───┘ └───┘ └───┘      │  Subtotal:        $35.00     │
│                          │  Discount:         $0.00     │
│  ┌───┐ ┌───┐ ┌───┐      │  Tax:              $3.50     │
│  │ D │ │ E │ │ F │      │  Total:           $38.50     │
│  └───┘ └───┘ └───┘      │                              │
│                          │  [💳 Checkout]               │
└──────────────────────────┴──────────────────────────────┘
```

## Usage

### Accessing the POS
1. Navigate to `/pos` in your browser
2. Must be authenticated (login required)
3. System loads all active products, customers, and promotions

### Making a Sale

#### Step 1: Select Customer (Optional)
- Choose from dropdown or leave as "Walk-in Customer"
- Members are marked with "(Member)" badge

#### Step 2: Select Promotion (Optional)
- Choose active promotion from dropdown
- Only promotions valid for current date are shown

#### Step 3: Add Products
- **Search**: Type product name or SKU in search box
- **Filter**: Click category tabs to filter products
- **Add**: Click product card to add to cart
- Products show stock levels if inventory tracking is enabled

#### Step 4: Manage Cart
- **Increase quantity**: Click `+` button
- **Decrease quantity**: Click `-` button
- **Remove item**: Click `×` button
- Cart updates automatically with totals

#### Step 5: Checkout
- Review order totals
- Click "💳 Checkout" button
- Order is created and cart is cleared
- Success message shows order number

## API Endpoints

### GET `/pos`
Returns POS interface with products, categories, customers, and promotions

### POST `/pos/checkout`
Creates a new order
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 10.00
    }
  ],
  "customer_id": 1,
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "promotion_ids": [1],
  "payment_method": "cash"
}
```

### GET `/pos/products?category_id=1&search=term`
Returns filtered products

### GET `/pos/customers?search=term`
Returns filtered customers

### GET `/pos/promotions`
Returns active promotions

## Features in Detail

### Product Cards
- **Visual design**: Gradient background with first letter
- **Information**: Name, price, stock level
- **Hover effect**: Lift animation with shadow
- **Click action**: Adds to cart

### Cart Management
- **Empty state**: Shows friendly message
- **Item display**: Product name, quantity controls, price
- **Quantity controls**: Increment/decrement buttons
- **Remove button**: Red × button
- **Auto-update**: Totals recalculate on changes

### Calculations
- **Subtotal**: Sum of all items (price × quantity)
- **Discount**: Applied from promotions (TODO: implement logic)
- **Tax**: 10% of subtotal
- **Total**: Subtotal - Discount + Tax

### Navigation
- **Dashboard**: Link to admin dashboard
- **Orders**: Link to orders management
- **Settings**: Placeholder for settings

## Technical Details

### Technologies Used
- **Backend**: Laravel (PHP)
- **Frontend**: Blade templates, Vanilla JavaScript
- **Styling**: Custom CSS with gradients
- **Icons**: Emoji-based (no external dependencies)

### Database Models
- `Product`: Product information and inventory
- `User`: Customer/member information
- `Promotion`: Promotion rules and dates
- `Order`: Order header information
- `OrderItem`: Individual order line items
- `Category`: Product categorization

### Security
- **Authentication**: Required for all POS routes
- **CSRF Protection**: Token included in checkout request
- **Input Validation**: Server-side validation on checkout
- **Transaction Safety**: Database transactions for order creation

## Customization

### Changing Colors
Edit the CSS gradient in `resources/views/pos/index.blade.php`:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Changing Tax Rate
Edit the JavaScript in the `updateCart()` function:
```javascript
const tax = subtotal * 0.1; // Change 0.1 to desired rate
```

### Adding Payment Methods
Update the checkout request to include payment method selection:
```javascript
payment_method: 'cash' // or 'card', 'mobile', etc.
```

## Future Enhancements

### Planned Features
- [ ] Promotion discount calculation integration
- [ ] Multiple payment methods UI
- [ ] Receipt printing
- [ ] Barcode scanner support
- [ ] Keyboard shortcuts
- [ ] Order history in POS
- [ ] Customer creation from POS
- [ ] Product quick add modal
- [ ] Split payment support
- [ ] Cash drawer integration

### Promotion Integration
The promotion system is ready but needs integration:
1. Apply promotion rules to cart items
2. Calculate discounts based on promotion type
3. Show applied promotions in cart
4. Log promotion usage

## Troubleshooting

### Products not showing
- Check if products are marked as `is_active = true`
- Verify products exist in database
- Check browser console for errors

### Checkout fails
- Ensure CSRF token is present
- Check product stock availability
- Verify user authentication
- Review server logs for errors

### Cart not updating
- Clear browser cache
- Check JavaScript console for errors
- Verify cart functions are defined

## Support
For issues or questions, refer to the main project documentation or contact the development team.
