# Promotion API Examples

This folder contains ready-to-use JSON examples for all promotion types matching the admin UI structure.

## 📁 Files

### Buy X Get Y Free
- **buy-x-get-y-free-any.json** - Buy any items, get cheapest free
- **buy-x-get-y-free-specific-product.json** - Buy specific product, get free
- **buy-x-get-y-free-specific-category.json** - Buy from category, get free
- **buy-x-get-specific-product-free.json** - Buy any items, get specific product free

### Step Discount
- **step-discount.json** - Different discounts by item position

### Fixed Price Bundle
- **fixed-price-bundle-any.json** - Any items for fixed price
- **fixed-price-bundle-specific-product.json** - Specific product bundle
- **fixed-price-bundle-specific-category.json** - Category bundle

### Percentage Discount
- **percentage-discount-all.json** - Discount on all products
- **percentage-discount-specific-products.json** - Discount on specific products
- **percentage-discount-specific-categories.json** - Discount on specific categories
- **fixed-amount-discount.json** - Fixed dollar amount off

## 🚀 Usage

### Test a single example:
```bash
curl -X POST http://localhost:8000/api/v1/promotions \
  -H "Content-Type: application/json" \
  -d @examples/buy-x-get-y-free-any.json
```

### Test all examples:
```bash
for file in examples/*.json; do
  echo "Testing $file..."
  curl -X POST http://localhost:8000/api/v1/promotions \
    -H "Content-Type: application/json" \
    -d @"$file"
  echo -e "\n---\n"
done
```

## 📝 Notes

- **Product/Category IDs**: Update the IDs in the examples to match your database
- **Codes**: Each promotion code must be unique
- **Structure**: All examples match the exact structure used by the admin UI

## 🔍 See Also

- **PROMOTION_TYPES_GUIDE.md** - Complete guide for all promotion types
- **API_DOCUMENTATION.md** - Full API reference
- **README_API.md** - Quick start guide
