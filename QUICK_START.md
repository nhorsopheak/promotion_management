# 🚀 Quick Start Guide

## Get Running in 5 Minutes

### 1. Start the Application
```bash
php artisan serve
```

### 2. Access Admin Panel
- URL: **http://localhost:8000/admin**
- Email: **admin@example.com**
- Password: **password**

---

## 🎯 Test Buy X Get Y Free Promotion

### Option 1: Using Tinker (Fastest)
```bash
php artisan tinker
```

```php
use App\Models\Product;
use App\Services\CartService;

$cart = app(CartService::class);

// Add 3 clothing items
$cart->addItem(Product::where('sku', 'CLO-001')->first(), 1); // T-Shirt $19.99
$cart->addItem(Product::where('sku', 'CLO-002')->first(), 1); // Jeans $49.99
$cart->addItem(Product::where('sku', 'CLO-003')->first(), 1); // Sneakers $79.99

// View cart with promotions applied
$result = $cart->getCartWithPromotions();
print_r($result);

// Expected:
// - Subtotal: $149.97
// - Discount: $19.99 (T-Shirt free)
// - Total: $129.98
```

### Option 2: Via Admin Panel
1. Go to **Marketing > Promotions**
2. See 2 active promotions:
   - **BUY2GET1** - Buy 2 Clothing, Get 1 Free
   - **BUY3GET2** - Buy 3 Any Items, Get 2 Free
3. Click to edit and see configuration

---

## 📦 What's Included

### Sample Data
- **2 Users** (admin & test)
- **5 Categories** (Electronics, Clothing, Skincare, Makeup, Food)
- **12 Products** (Various prices $12.99 - $999.99)
- **2 Active Promotions** (Buy X Get Y Free)

### Admin Features
- **Promotions** - Create/Edit/Delete promotions
- **Products** - Manage product catalog
- **Categories** - Organize products

---

## 🧪 Run Tests
```bash
php artisan test --filter=BuyXGetYFreePromotionTest
```

Expected: **5 passed (17 assertions)**

---

## 📝 Create Your First Promotion

### Via Admin Panel
1. Go to **Marketing > Promotions**
2. Click **New Promotion**
3. Fill in:
   - **Name:** "Summer Sale"
   - **Code:** "SUMMER2024"
   - **Type:** "Buy X Get Y Free"
   - **Buy Quantity:** 2
   - **Get Quantity:** 1
   - **Status:** Active
4. Save

### Via Tinker
```php
use App\Models\Promotion;
use App\Enums\PromotionType;
use App\Enums\PromotionStatus;

Promotion::create([
    'code' => 'SUMMER2024',
    'name' => 'Summer Sale - Buy 2 Get 1',
    'type' => PromotionType::BUY_X_GET_Y_FREE->value,
    'status' => PromotionStatus::ACTIVE->value,
    'start_date' => now(),
    'end_date' => now()->addMonth(),
    'priority' => 10,
    'conditions' => [
        'buy_quantity' => 2,
        'get_quantity' => 1,
        'apply_to_cheapest' => true,
        'eligible_product_ids' => [],
        'eligible_category_ids' => [],
    ],
    'is_active' => true,
]);
```

---

## 🔍 Explore the Code

### Key Files to Check
```
app/Services/Promotions/Strategies/BuyXGetYFreeStrategy.php
app/Services/Promotions/PromotionEngine.php
app/Services/CartService.php
app/Filament/Resources/PromotionResource.php
```

### Test Files
```
tests/Feature/BuyXGetYFreePromotionTest.php
```

---

## 📚 Documentation

- **TASKS.md** - Roadmap for all 20 promotion types
- **SETUP.md** - Detailed setup instructions
- **IMPLEMENTATION_SUMMARY.md** - What's been built
- **This file** - Quick start guide

---

## 🎯 Next Steps

1. ✅ Test the Buy X Get Y Free promotion
2. ✅ Explore the admin panel
3. ✅ Review the code architecture
4. 📝 Implement next promotion type (see TASKS.md)
5. 🚀 Deploy to production

---

## 💡 Tips

- **Reset Database:** `php artisan migrate:fresh --seed`
- **Clear Cache:** `php artisan cache:clear`
- **View Routes:** `php artisan route:list`
- **Check Migrations:** `php artisan migrate:status`

---

## 🆘 Troubleshooting

**Promotions not applying?**
- Check promotion status is "active"
- Verify start/end dates
- Ensure cart items match conditions

**Admin panel not loading?**
- Run: `php artisan filament:upgrade`
- Clear cache: `php artisan cache:clear`

**Database errors?**
- Fresh start: `php artisan migrate:fresh --seed`

---

## ✨ Features Implemented

✅ Buy X Get Y Free promotion  
✅ Automatic discount application  
✅ Category/Product restrictions  
✅ Cheapest item selection  
✅ Multiple sets support  
✅ Usage limits  
✅ Date/time scheduling  
✅ Admin panel management  
✅ Comprehensive testing  

---

**Ready to build more?** Check **TASKS.md** for the next 19 promotion types!
