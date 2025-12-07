## PHASE 4: API Backend (7-10 days)

### 4.1 API Foundation
**Goal**: Setup versioned API with authentication

**Tasks**:
1. Create `routes/api.php` with `/api/v1` prefix
2. Install/configure Laravel Sanctum for API auth
3. Create BaseResource, ApiResponse helper classes
4. Create ApiExceptionHandler for consistent error responses

**Files to Create**:
- `routes/api.php`
- `app/Http/Resources/BaseResource.php`
- `app/Http/Responses/ApiResponse.php`
- `app/Exceptions/ApiExceptionHandler.php`

**Files to Modify**:
- `bootstrap/app.php` (register API routes)

### 4.2 Category & Nomenclature API
**Goal**: Expose categories and nomenclatures

**Tasks**:
1. Create CategoryResource (API) with bilingual names, slug, icon, specs
2. Create CategoryController: index(), show($slug)
3. Create NomenclatureResource (API)
4. Add routes: GET /categories, GET /categories/{slug}, GET /categories/{slug}/nomenclatures
5. Cache responses (60 min for categories)

**Files to Create**:
- `app/Http/Resources/Api/V1/CategoryResource.php`
- `app/Http/Resources/Api/V1/NomenclatureResource.php`
- `app/Http/Controllers/Api/V1/CategoryController.php`

### 4.3 Product Search & Filtering API
**Goal**: Advanced product search with spec filters

**Tasks**:
1. Create ProductResource and ProductCollection (API)
2. Create ProductController: index(), show(), search()
3. Create ProductQueryBuilder with filters (category, city, price_min/max, in_stock, specs)
4. Create ProductFilterService for dynamic spec filtering
5. Create ProductSearchService for full-text search
6. Add routes: GET /products, GET /products/{id}, GET /categories/{slug}/products

**Files to Create**:
- `app/Http/Resources/Api/V1/ProductResource.php`
- `app/Http/Resources/Api/V1/ProductCollection.php`
- `app/Http/Controllers/Api/V1/ProductController.php`
- `app/QueryBuilders/ProductQueryBuilder.php`
- `app/Services/ProductFilterService.php`
- `app/Services/ProductSearchService.php`

**Testing**: `tests/Feature/Api/ProductApiTest.php`

### 4.4 Cart API
**Goal**: Shopping cart management

**Tasks**:
1. Create migrations: carts table, cart_items table
2. Create Cart and CartItem models with relationships
3. Create CartResource (API) with items, total, items_count
4. Create CartController: show(), addItem(), updateItem(), removeItem(), clear()
5. Create AddToCartAction, UpdateCartItemAction (validate stock, handle price snapshots)
6. Add routes: GET /cart, POST /cart/items, PATCH /cart/items/{id}, DELETE /cart/items/{id}

**Files to Create**:
- `database/migrations/2025_12_08_000003_create_carts_table.php`
- `database/migrations/2025_12_08_000004_create_cart_items_table.php`
- `app/Models/Cart.php`, `app/Models/CartItem.php`
- `app/Http/Resources/Api/V1/CartResource.php`
- `app/Http/Controllers/Api/V1/CartController.php`
- `app/Actions/Cart/AddToCartAction.php`, `UpdateCartItemAction.php`

### 4.5 Order & Checkout API
**Goal**: Order placement and management

**Tasks**:
1. Create migrations: orders table (with order_number, status enum), order_items table
2. Create Order and OrderItem models with relationships
3. Create OrderResource (API)
4. Create OrderController: index(), store(), show(), cancel()
5. Create CreateOrderFromCartAction (validate stock, create order, clear cart, send notifications)
6. Create StoreOrderRequest (validate delivery info, cart not empty, stock available)
7. Add routes: GET /orders, POST /orders, GET /orders/{id}, POST /orders/{id}/cancel (auth required)

**Files to Create**:
- `database/migrations/2025_12_08_000005_create_orders_table.php`
- `database/migrations/2025_12_08_000006_create_order_items_table.php`
- `app/Models/Order.php`, `app/Models/OrderItem.php`
- `app/Http/Resources/Api/V1/OrderResource.php`
- `app/Http/Controllers/Api/V1/OrderController.php`
- `app/Actions/Order/CreateOrderFromCartAction.php`
- `app/Http/Requests/Api/V1/StoreOrderRequest.php`

**Testing**: `tests/Feature/Api/OrderApiTest.php`

---
