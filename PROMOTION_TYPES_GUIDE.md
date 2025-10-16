# Promotion Types Guide

This guide shows the exact JSON structure for each promotion type, matching the admin UI configuration.

## 📋 Table of Contents

1. [Buy X Get Y Free](#1-buy-x-get-y-free)
2. [Step Discount](#2-step-discount)
3. [Fixed Price Bundle](#3-fixed-price-bundle)
4. [Percentage Discount](#4-percentage-discount)

---

## 1. Buy X Get Y Free

### 1a. Any Products (Cheapest Free)
Customer buys any X items and gets Y cheapest items free.

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

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/buy-x-get-y-free-any.json
```

### 1b. Specific Product
Customer buys X of a specific product and gets Y free.

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

**Note:** `apply_to_product_ids` is a single product ID (integer), not an array.

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/buy-x-get-y-free-specific-product.json
```

### 1c. Specific Category
Customer buys X items from a category and gets Y free.

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

**Note:** `apply_to_category_ids` is a single category ID (integer), not an array.

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/buy-x-get-y-free-specific-category.json
```

### 1d. Get Specific Product Free
Customer buys any X items and gets a specific product free.

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

**Note:** `get_product_ids` is a single product ID (integer), not an array.

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/buy-x-get-specific-product-free.json
```

### Buy X Get Y Free - Field Reference

| Field | Type | Required | Options | Description |
|-------|------|----------|---------|-------------|
| `buy_quantity` | integer | Yes | - | Number of items to buy |
| `get_quantity` | integer | Yes | - | Number of free items |
| `apply_to_type` | string | Yes | `any`, `specific_products`, `specific_categories` | What items qualify for purchase |
| `apply_to_product_ids` | integer | Conditional | - | Product ID (required if `apply_to_type` = `specific_products`) |
| `apply_to_category_ids` | integer | Conditional | - | Category ID (required if `apply_to_type` = `specific_categories`) |
| `get_type` | string | Yes | `cheapest`, `specific_products` | What items are given free |
| `get_product_ids` | integer | Conditional | - | Product ID (required if `get_type` = `specific_products`) |
| `apply_to_cheapest` | boolean | Optional | - | Give cheapest items free (used with `get_type` = `cheapest`) |

---

## 2. Step Discount

Different discount percentages based on item position (cheapest items first).

```json
{
  "code": "STEP50",
  "name": "Step Discount - Up to 50% Off",
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

**Example:** 
- 1st item (cheapest): 0% off
- 2nd item: 20% off
- 3rd item: 30% off
- 4th item: 0% off
- 5th item: 50% off

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/step-discount.json
```

### Step Discount - Field Reference

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `discount_tiers` | array | Yes | Array of position/percentage objects |
| `discount_tiers[].position` | integer | Yes | Item position (2 or higher) |
| `discount_tiers[].percentage` | integer | Yes | Discount percentage (0-100) |

---

## 3. Fixed Price Bundle

Buy a specific quantity for a fixed total price.

### 3a. Any Products

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

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/fixed-price-bundle-any.json
```

### 3b. Specific Product

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

**Note:** `eligible_product_ids` is a single product ID (integer), not an array.

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/fixed-price-bundle-specific-product.json
```

### 3c. Specific Category

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

**Note:** `eligible_category_ids` is a single category ID (integer), not an array.

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/fixed-price-bundle-specific-category.json
```

### Fixed Price Bundle - Field Reference

| Field | Type | Required | Options | Description |
|-------|------|----------|---------|-------------|
| `bundle_quantity` | integer | Yes | - | Number of items in bundle |
| `bundle_price` | decimal | Yes | - | Fixed price for the bundle |
| `bundle_type` | string | Yes | `any`, `specific_products`, `specific_categories` | What items are eligible |
| `eligible_product_ids` | integer | Conditional | - | Product ID (required if `bundle_type` = `specific_products`) |
| `eligible_category_ids` | integer | Conditional | - | Category ID (required if `bundle_type` = `specific_categories`) |

---

## 4. Percentage Discount

Apply percentage or fixed amount discount.

### 4a. All Products (Percentage)

```json
{
  "code": "SAVE20",
  "name": "Save 20% on Everything",
  "type": "percentage_discount",
  "status": "active",
  "conditions": {
    "discount_type": "percentage",
    "discount_value": 20,
    "apply_to_type": "all"
  }
}
```

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/percentage-discount-all.json
```

### 4b. Specific Products

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

**Note:** `eligible_product_ids` is an array of product IDs for percentage discount type.

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/percentage-discount-specific-products.json
```

### 4c. Specific Categories

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

**Note:** `eligible_category_ids` is an array of category IDs for percentage discount type.

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/percentage-discount-specific-categories.json
```

### 4d. Fixed Amount Discount

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

**Test:**
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/fixed-amount-discount.json
```

### Percentage Discount - Field Reference

| Field | Type | Required | Options | Description |
|-------|------|----------|---------|-------------|
| `discount_type` | string | Yes | `percentage`, `fixed_amount` | Type of discount |
| `discount_value` | decimal | Yes | - | Percentage (0-100) or fixed amount |
| `apply_to_type` | string | Yes | `all`, `specific_products`, `specific_categories` | What items get the discount |
| `eligible_product_ids` | array | Conditional | - | Array of product IDs (required if `apply_to_type` = `specific_products`) |
| `eligible_category_ids` | array | Conditional | - | Array of category IDs (required if `apply_to_type` = `specific_categories`) |

---

## 🔑 Key Differences Summary

### Product/Category IDs

| Promotion Type | Field Name | Data Type |
|----------------|------------|-----------|
| Buy X Get Y Free (apply to) | `apply_to_product_ids` | **Single integer** |
| Buy X Get Y Free (apply to) | `apply_to_category_ids` | **Single integer** |
| Buy X Get Y Free (get free) | `get_product_ids` | **Single integer** |
| Fixed Price Bundle | `eligible_product_ids` | **Single integer** |
| Fixed Price Bundle | `eligible_category_ids` | **Single integer** |
| Percentage Discount | `eligible_product_ids` | **Array of integers** |
| Percentage Discount | `eligible_category_ids` | **Array of integers** |

### Field Names by Type

| Promotion Type | Quantity Field | Price/Discount Field | Tier Field |
|----------------|----------------|----------------------|------------|
| Buy X Get Y Free | `buy_quantity`, `get_quantity` | - | - |
| Step Discount | - | - | `discount_tiers` |
| Fixed Price Bundle | `bundle_quantity` | `bundle_price` | - |
| Percentage Discount | - | `discount_value` | - |

---

## 📝 Testing All Examples

Test all promotion types at once:

```bash
# Buy X Get Y Free
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/buy-x-get-y-free-any.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/buy-x-get-y-free-specific-product.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/buy-x-get-y-free-specific-category.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/buy-x-get-specific-product-free.json

# Step Discount
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/step-discount.json

# Fixed Price Bundle
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/fixed-price-bundle-any.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/fixed-price-bundle-specific-product.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/fixed-price-bundle-specific-category.json

# Percentage Discount
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/percentage-discount-all.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/percentage-discount-specific-products.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/percentage-discount-specific-categories.json
curl -X POST http://localhost:8000/api/v1/promotions -H "Content-Type: application/json" -d @examples/fixed-amount-discount.json
```

---

## 🎯 Quick Reference

**All promotion types support these common fields:**
- `code` (required, unique)
- `name` (required)
- `description` (optional)
- `type` (required)
- `status` (required)
- `start_date` (optional)
- `end_date` (optional)
- `start_time` (optional)
- `end_time` (optional)
- `days_of_week` (optional, array of 0-6)

**Product selection is done in the admin UI, stored in `conditions` JSON field.**
