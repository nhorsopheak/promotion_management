# Promotion Management API Documentation

## Create Promotion API

### Endpoint
```
POST /api/v1/promotions
```

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Request Body

#### Required Fields
- **code** (string, max: 255): Unique promotion code
- **name** (string, max: 255): Promotion name
- **type** (string): Promotion type, one of:
  - `buy_x_get_y_free`
  - `step_discount`
  - `fixed_price_bundle`
  - `percentage_discount`
- **status** (string): Promotion status, one of:
  - `draft`
  - `scheduled`
  - `active`
  - `paused`
  - `expired`
  - `cancelled`

#### Optional Fields
- **description** (string): Promotion description
- **start_date** (datetime): Start date of the promotion
- **end_date** (datetime): End date of the promotion (must be after or equal to start_date)
- **start_time** (time, format: HH:mm:ss): Start time of the promotion
- **end_time** (time, format: HH:mm:ss): End time of the promotion
- **days_of_week** (array of integers): Days when promotion is active (0=Sunday, 6=Saturday)
- **conditions** (object): Promotion conditions (JSON object)
- **benefits** (object): Promotion benefits (JSON object)

### Example Request

```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "code": "SUMMER2024",
    "name": "Summer Sale 2024",
    "description": "Get amazing discounts this summer",
    "type": "percentage_discount",
    "status": "active",
    "start_date": "2024-06-01 00:00:00",
    "end_date": "2024-08-31 23:59:59",
    "days_of_week": [1, 2, 3, 4, 5],
    "conditions": {
      "min_purchase_amount": 100,
      "max_discount_amount": 50
    },
    "benefits": {
      "discount_percentage": 20
    }
  }'
```

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Promotion created successfully",
  "data": {
    "id": 1,
    "code": "SUMMER2024",
    "name": "Summer Sale 2024",
    "description": "Get amazing discounts this summer",
    "type": "percentage_discount",
    "status": "active",
    "start_date": "2024-06-01T00:00:00.000000Z",
    "end_date": "2024-08-31T23:59:59.000000Z",
    "start_time": null,
    "end_time": null,
    "days_of_week": [1, 2, 3, 4, 5],
    "conditions": {
      "min_purchase_amount": 100,
      "max_discount_amount": 50
    },
    "benefits": {
      "discount_percentage": 20
    },
    "created_at": "2024-10-15T08:37:00.000000Z",
    "updated_at": "2024-10-15T08:37:00.000000Z"
  }
}
```

### Error Response (422 Unprocessable Entity)

```json
{
  "message": "The code has already been taken. (and 1 more error)",
  "errors": {
    "code": [
      "The code has already been taken."
    ],
    "type": [
      "The selected promotion type is invalid."
    ]
  }
}
```

### Example Requests for Different Promotion Types

#### 1. Buy X Get Y Free (Any Products)
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

#### 1b. Buy X Get Y Free (Specific Products)
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

#### 1c. Buy X Get Y Free (Specific Category)
```json
{
  "code": "BUY2GET1DRINKS",
  "name": "Buy 2 Drinks Get 1 Free",
  "type": "buy_x_get_y_free",
  "status": "active",
  "conditions": {
    "buy_quantity": 2,
    "get_quantity": 1,
    "apply_to_type": "specific_categories",
    "apply_to_category_ids": 3,
    "get_type": "cheapest",
    "apply_to_cheapest": true
  }
}
```

#### 1d. Buy X Get Specific Product Free
```json
{
  "code": "BUY2GETWATER",
  "name": "Buy 2 Get Water Free",
  "type": "buy_x_get_y_free",
  "status": "active",
  "conditions": {
    "buy_quantity": 2,
    "get_quantity": 1,
    "apply_to_type": "any",
    "get_type": "specific_products",
    "get_product_ids": 10
  }
}
```

#### 2. Step Discount
```json
{
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
}
```

#### 3. Fixed Price Bundle (Any Products)
```json
{
  "code": "BUNDLE99",
  "name": "3 for $99",
  "type": "fixed_price_bundle",
  "status": "active",
  "conditions": {
    "bundle_quantity": 3,
    "bundle_price": 99.00,
    "bundle_type": "any"
  }
}
```

#### 3b. Fixed Price Bundle (Specific Product)
```json
{
  "code": "BUNDLE3SHIRTS",
  "name": "3 Shirts for $99",
  "type": "fixed_price_bundle",
  "status": "active",
  "conditions": {
    "bundle_quantity": 3,
    "bundle_price": 99.00,
    "bundle_type": "specific_products",
    "eligible_product_ids": 15
  }
}
```

#### 3c. Fixed Price Bundle (Specific Category)
```json
{
  "code": "BUNDLE3SNACKS",
  "name": "3 Snacks for $10",
  "type": "fixed_price_bundle",
  "status": "active",
  "conditions": {
    "bundle_quantity": 3,
    "bundle_price": 10.00,
    "bundle_type": "specific_categories",
    "eligible_category_ids": 5
  }
}
```

#### 4. Percentage Discount (All Products)
```json
{
  "code": "SAVE20",
  "name": "Save 20%",
  "type": "percentage_discount",
  "status": "active",
  "conditions": {
    "discount_type": "percentage",
    "discount_value": 20,
    "apply_to_type": "all"
  }
}
```

#### 4b. Percentage Discount (Specific Products)
```json
{
  "code": "ELECTRONICS15",
  "name": "15% Off Electronics",
  "type": "percentage_discount",
  "status": "active",
  "conditions": {
    "discount_type": "percentage",
    "discount_value": 15,
    "apply_to_type": "specific_products",
    "eligible_product_ids": [20, 21, 22]
  }
}
```

#### 4c. Percentage Discount (Specific Categories)
```json
{
  "code": "CLOTHING25",
  "name": "25% Off Clothing",
  "type": "percentage_discount",
  "status": "active",
  "conditions": {
    "discount_type": "percentage",
    "discount_value": 25,
    "apply_to_type": "specific_categories",
    "eligible_category_ids": [1, 2]
  }
}
```

#### 4d. Fixed Amount Discount
```json
{
  "code": "SAVE5",
  "name": "$5 Off",
  "type": "percentage_discount",
  "status": "active",
  "conditions": {
    "discount_type": "fixed_amount",
    "discount_value": 5.00,
    "apply_to_type": "all"
  }
}
```

## Testing the API

### Using cURL
```bash
# Test the API
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d @promotion.json
```

### Using Postman
1. Create a new POST request
2. Set URL to: `http://localhost:8000/api/v1/promotions`
3. Set Headers:
   - `Content-Type: application/json`
   - `Accept: application/json`
4. Add JSON body with promotion data
5. Send the request

### Using PHP/Laravel HTTP Client
```php
use Illuminate\Support\Facades\Http;

$response = Http::post('http://localhost:8000/api/v1/promotions', [
    'code' => 'TEST123',
    'name' => 'Test Promotion',
    'type' => 'percentage_discount',
    'status' => 'active',
    'conditions' => [
        'min_purchase_amount' => 100
    ],
    'benefits' => [
        'discount_percentage' => 15
    ]
]);

$data = $response->json();
```

## Notes

- All datetime fields should be in ISO 8601 format
- The API uses Laravel's validation, so all validation errors will be returned with a 422 status code
- The promotion code must be unique across all promotions
- The `conditions` and `benefits` fields accept any valid JSON object structure
- Days of week: 0 = Sunday, 1 = Monday, ..., 6 = Saturday
