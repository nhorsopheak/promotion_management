# Quick API Guide - Create Promotion

## 🚀 Quick Start

Your API is ready to use! Here's everything you need to know:

### Endpoint
```
POST /api/v1/promotions
```

### Test the API

#### Option 1: Using cURL
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "code": "TEST123",
    "name": "Test Promotion",
    "type": "percentage_discount",
    "status": "active",
    "conditions": {"min_purchase_amount": 100},
    "benefits": {"discount_percentage": 20}
  }'
```

#### Option 2: Run Tests
```bash
php artisan test --filter CreatePromotionTest
```

## 📋 Required Fields

| Field | Type | Values |
|-------|------|--------|
| code | string | Unique code (e.g., "SUMMER2024") |
| name | string | Promotion name |
| type | string | `buy_x_get_y_free`, `step_discount`, `fixed_price_bundle`, `percentage_discount` |
| status | string | `draft`, `scheduled`, `active`, `paused`, `expired`, `cancelled` |

## 📝 Optional Fields

- **description**: Text description
- **start_date**: When promotion starts (datetime)
- **end_date**: When promotion ends (datetime)
- **start_time**: Daily start time (HH:mm:ss)
- **end_time**: Daily end time (HH:mm:ss)
- **days_of_week**: Array of days [0-6] (0=Sunday)
- **conditions**: JSON object with promotion rules
- **benefits**: JSON object with promotion benefits

## 🎯 Quick Examples

### 1. Simple Percentage Discount
```json
{
  "code": "SAVE20",
  "name": "Save 20%",
  "type": "percentage_discount",
  "status": "active",
  "benefits": {"discount_percentage": 20}
}
```

### 2. Buy 2 Get 1 Free (Any Products)
```json
{
  "code": "BUY2GET1",
  "name": "Buy 2 Get 1 Free",
  "type": "buy_x_get_y_free",
  "status": "active",
  "conditions": {
    "buy_quantity": 2,
    "get_quantity": 1,
    "apply_to_type": "any",
    "get_type": "cheapest",
    "apply_to_cheapest": true
  }
}
```

### 2b. Buy 2 Get 1 Free (Specific Product)
```json
{
  "code": "BUY2GET1COLA",
  "name": "Buy 2 Colas Get 1 Free",
  "type": "buy_x_get_y_free",
  "status": "active",
  "conditions": {
    "buy_quantity": 2,
    "get_quantity": 1,
    "apply_to_type": "specific_products",
    "apply_to_product_ids": 5,
    "get_type": "cheapest",
    "apply_to_cheapest": true
  }
}
```

### 3. Weekend Special
```json
{
  "code": "WEEKEND50",
  "name": "Weekend 50% Off",
  "type": "percentage_discount",
  "status": "active",
  "days_of_week": [0, 6],
  "benefits": {"discount_percentage": 50}
}
```

## ✅ Success Response (201)
```json
{
  "success": true,
  "message": "Promotion created successfully",
  "data": {
    "id": 1,
    "code": "TEST123",
    "name": "Test Promotion",
    ...
  }
}
```

## ❌ Error Response (422)
```json
{
  "message": "The code has already been taken.",
  "errors": {
    "code": ["The code has already been taken."]
  }
}
```

## 🏗️ Architecture

This API follows the **Single Action Controller** pattern:

```
routes/api.php
  └─> CreatePromotionController::__invoke()
       └─> CreatePromotionRequest (validation)
            └─> Promotion::create()
```

### Files Created
1. **Controller**: `app/Http/Controllers/Api/CreatePromotionController.php`
2. **Request**: `app/Http/Requests/CreatePromotionRequest.php`
3. **Routes**: `routes/api.php`
4. **Factory**: `database/factories/PromotionFactory.php`
5. **Tests**: `tests/Feature/Api/CreatePromotionTest.php`

## 🧪 Running Tests

```bash
# Run all API tests
php artisan test tests/Feature/Api/CreatePromotionTest.php

# Run specific test
php artisan test --filter it_can_create_a_promotion_with_valid_data

# Run with coverage
php artisan test --coverage
```

## 📚 Full Documentation

See `API_DOCUMENTATION.md` for complete details including:
- All validation rules
- Complete field descriptions
- More examples for each promotion type
- Error handling details
