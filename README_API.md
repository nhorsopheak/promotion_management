# 🎯 Promotion Management API

## Overview

This API allows you to create promotions using a clean, invokable controller pattern where each controller has only one function (`__invoke`).

## ✅ Status: FULLY FUNCTIONAL & TESTED

- ✅ API endpoint created and working
- ✅ All validations implemented
- ✅ 9 comprehensive tests (all passing)
- ✅ Database migration completed
- ✅ Live tested and verified

## 🚀 Quick Start

### 1. Start the Server (if not running)
```bash
php artisan serve
```

### 2. Test the API
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "code": "SAVE20",
    "name": "Save 20%",
    "type": "percentage_discount",
    "status": "active",
    "benefits": {"discount_percentage": 20}
  }'
```

### 3. Expected Response (201 Created)
```json
{
  "success": true,
  "message": "Promotion created successfully",
  "data": {
    "id": 1,
    "code": "SAVE20",
    "name": "Save 20%",
    "type": "percentage_discount",
    "status": "active",
    "benefits": {"discount_percentage": 20},
    "created_at": "2025-10-15T01:43:53.000000Z",
    "updated_at": "2025-10-15T01:43:53.000000Z"
  }
}
```

## 📋 API Endpoint

```
POST /api/v1/promotions
```

**Headers:**
- `Content-Type: application/json`
- `Accept: application/json`

## 📝 Request Fields

### Required Fields
| Field | Type | Description | Example |
|-------|------|-------------|---------|
| code | string | Unique promotion code | "SUMMER2024" |
| name | string | Promotion name | "Summer Sale" |
| type | enum | Promotion type | "percentage_discount" |
| status | enum | Promotion status | "active" |

### Optional Fields
| Field | Type | Description | Example |
|-------|------|-------------|---------|
| description | string | Promotion description | "Get 20% off" |
| start_date | datetime | Start date | "2024-06-01 00:00:00" |
| end_date | datetime | End date | "2024-08-31 23:59:59" |
| start_time | time | Daily start time | "09:00:00" |
| end_time | time | Daily end time | "21:00:00" |
| days_of_week | array | Active days (0-6) | [1,2,3,4,5] |
| conditions | object | Promotion rules | {"min_amount": 100} |
| benefits | object | Promotion benefits | {"discount": 20} |

### Promotion Types
- `percentage_discount` - Percentage off
- `buy_x_get_y_free` - Buy X Get Y Free
- `step_discount` - Tiered discounts
- `fixed_price_bundle` - Fixed bundle price

### Promotion Status
- `draft` - Not yet active
- `scheduled` - Scheduled for future
- `active` - Currently active
- `paused` - Temporarily paused
- `expired` - Past end date
- `cancelled` - Cancelled

## 📚 Example Requests

### 1. Simple Percentage Discount
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SAVE20",
    "name": "Save 20%",
    "type": "percentage_discount",
    "status": "active",
    "benefits": {"discount_percentage": 20}
  }'
```

### 2. Buy 2 Get 1 Free (Any Products)
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{
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
  }'
```

### 2b. Buy 2 Get 1 Free (Specific Product - Product ID 5)
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{
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
  }'
```

### 3. Weekend Special (with time restrictions)
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{
    "code": "WEEKEND50",
    "name": "Weekend 50% Off",
    "type": "percentage_discount",
    "status": "active",
    "days_of_week": [0, 6],
    "start_time": "09:00:00",
    "end_time": "21:00:00",
    "conditions": {
      "discount_type": "percentage",
      "discount_value": 50,
      "apply_to_type": "all"
    }
  }'
```

### 4. Scheduled Promotion
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SUMMER2024",
    "name": "Summer Sale 2024",
    "type": "percentage_discount",
    "status": "scheduled",
    "start_date": "2024-06-01 00:00:00",
    "end_date": "2024-08-31 23:59:59",
    "conditions": {
      "discount_type": "percentage",
      "discount_value": 25,
      "apply_to_type": "all"
    }
  }'
```

### 5. Fixed Price Bundle (3 for $99)
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{
    "code": "BUNDLE99",
    "name": "3 for $99",
    "type": "fixed_price_bundle",
    "status": "active",
    "conditions": {
      "bundle_quantity": 3,
      "bundle_price": 99.00,
      "bundle_type": "any"
    }
  }'
```

### 6. Step Discount
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{
    "code": "STEP50",
    "name": "Step Discount 50%",
    "type": "step_discount",
    "status": "active",
    "conditions": {
      "discount_tiers": [
        {"position": 2, "percentage": 20},
        {"position": 3, "percentage": 30},
        {"position": 5, "percentage": 50}
      ]
    }
  }'
```

## ❌ Error Responses

### Validation Error (422)
```json
{
  "message": "The code has already been taken.",
  "errors": {
    "code": ["The code has already been taken."]
  }
}
```

### Invalid Type (422)
```json
{
  "message": "The selected promotion type is invalid.",
  "errors": {
    "type": ["The selected promotion type is invalid."]
  }
}
```

## 🧪 Testing

### Run All Tests
```bash
php artisan test --filter CreatePromotionTest
```

### Run Specific Test
```bash
php artisan test --filter it_can_create_a_promotion_with_valid_data
```

### Test Results
```
✓ it can create a promotion with valid data
✓ it validates required fields
✓ it validates unique promotion code
✓ it validates promotion type
✓ it validates promotion status
✓ it validates end date is after start date
✓ it can create buy x get y free promotion
✓ it can create step discount promotion
✓ it can create fixed price bundle promotion

Tests: 9 passed (43 assertions)
```

## 🏗️ Architecture

### Invokable Controller Pattern
```php
// routes/api.php
Route::post('/promotions', CreatePromotionController::class);

// CreatePromotionController.php
class CreatePromotionController extends Controller
{
    public function __invoke(CreatePromotionRequest $request): JsonResponse
    {
        $promotion = Promotion::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Promotion created successfully',
            'data' => $promotion,
        ], 201);
    }
}
```

### Benefits of This Pattern
- ✅ Single Responsibility Principle
- ✅ Clean and focused controllers
- ✅ Easy to test
- ✅ Clear intent
- ✅ Better organization

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── CreatePromotionController.php  # Invokable controller
│   └── Requests/
│       └── CreatePromotionRequest.php         # Validation
├── Models/
│   └── Promotion.php                          # Model
database/
├── factories/
│   └── PromotionFactory.php                   # Factory for testing
└── migrations/
    ├── 2024_01_01_000005_create_promotions_table.php
    └── 2025_10_15_013953_add_time_fields_to_promotions_table.php
routes/
└── api.php                                    # API routes
tests/
└── Feature/
    └── Api/
        └── CreatePromotionTest.php            # Feature tests
```

## 📖 Additional Documentation

- **Full API Reference**: See `API_DOCUMENTATION.md`
- **Quick Guide**: See `QUICK_API_GUIDE.md`
- **Implementation Details**: See `API_IMPLEMENTATION_SUMMARY.md`
- **Example Payload**: See `example-promotion.json`

## 🎉 Live Test Verification

The API has been live tested and verified:

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d '{"code":"TEST001","name":"Test Promotion","type":"percentage_discount","status":"active","benefits":{"discount_percentage":15}}'
```

**Response:**
```json
{
  "success": true,
  "message": "Promotion created successfully",
  "data": {
    "id": 1,
    "code": "TEST001",
    "name": "Test Promotion",
    "type": "percentage_discount",
    "status": "active",
    "benefits": {"discount_percentage": 15},
    "created_at": "2025-10-15T01:43:53.000000Z",
    "updated_at": "2025-10-15T01:43:53.000000Z"
  }
}
```

**Database Verification:** ✅ Data successfully saved

## 💡 Tips

1. **Use the example file**: Test with `curl -d @example-promotion.json`
2. **Check validation**: The API provides detailed error messages
3. **Test different types**: Try all 4 promotion types
4. **Use Postman**: Import the endpoint for easier testing
5. **Run tests first**: Verify everything works with `php artisan test`

## 🔧 Troubleshooting

### Issue: 404 Not Found
**Solution**: Make sure the server is running: `php artisan serve`

### Issue: 422 Validation Error
**Solution**: Check the error response for specific field issues

### Issue: 500 Server Error
**Solution**: Check the logs: `tail -f storage/logs/laravel.log`

## 🎯 Next Steps

Your API is ready to use! You can:
1. ✅ Start creating promotions via API
2. ✅ Integrate with your frontend
3. ✅ Add authentication if needed
4. ✅ Extend with more CRUD operations
5. ✅ Add API documentation (Swagger/OpenAPI)

---

**Happy Coding! 🚀**
