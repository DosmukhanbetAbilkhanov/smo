# 10. Critical Files Reference

The following files are most critical for implementing this marketplace system:

## Core Domain Models
1. **`app/Models/Category.php`**
   - Hierarchical category model with self-referential parent/child relationships
   - Manages category-specification associations via `category_specs` pivot
   - Foundation for the entire product specification system

2. **`app/Models/Product.php`**
   - Main business entity connecting nomenclatures, shops, and specifications
   - Handles product images (JSON casting), pricing, stock quantity
   - Central to cart, orders, and catalog features

3. **`app/Models/Nomenclature.php`**
   - Master product data template
   - Links categories to units, stores bilingual descriptions
   - Used by sellers to create product instances

## Critical Business Logic

4. **`app/QueryBuilders/ProductQueryBuilder.php`** (to create)
   - Handles complex product filtering logic
   - Implements category hierarchy traversal (parent → children)
   - Dynamic specification filtering based on category
   - Performance-critical for catalog pages

5. **`app/Actions/Order/CreateOrderFromCartAction.php`** (to create)
   - Core order placement business logic
   - Validates stock availability across multiple shops
   - Creates orders, order items, handles cart clearing
   - Triggers order notifications to customers and sellers
   - Critical for transaction integrity

6. **`app/Services/ProductFilterService.php`** (to create)
   - Processes dynamic spec filters from API requests
   - Parses query params like `?specs[Цвет]=Красный&specs[Размер]=10`
   - Joins with `product_specs` table efficiently
   - Essential for frontend filter functionality

## Primary User Interfaces

7. **`app/Filament/Seller/Resources/ProductResource.php`** (to create)
   - Primary interface for sellers to manage inventory
   - Implements category-spec validation (required specs must be filled)
   - Handles image uploads and processing
   - Auto-populates spec inputs based on selected category
   - Most complex Filament resource in the system

8. **`resources/js/Pages/Catalog/Products.vue`** (to create)
   - Customer-facing product catalog page
   - Integrates filtering sidebar, sorting, pagination
   - Uses Inertia deferred props for performance
   - Performance-critical frontend component
   - Foundation for customer browse experience

## API & Integration

9. **`routes/api.php`** (to create)
   - API contract definition for frontend consumption
   - Versioning strategy (`/api/v1`)
   - Rate limiting configuration (prevent abuse)
   - Security and authentication middleware

10. **`app/Http/Controllers/Api/V1/ProductController.php`** (to create)
    - Exposes product search, filtering, and detail endpoints
    - Integrates ProductQueryBuilder and ProductFilterService
    - Returns paginated results via ProductCollection
    - Most-used API controller

---