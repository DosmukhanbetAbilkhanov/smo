## PHASE 5: Frontend (Vue 3 + Inertia) (10-14 days)

### 5.1 Frontend Architecture Setup
**Goal**: Setup Pinia stores, API client, routing

**Tasks**:
1. Create API client service with axios (base URL, interceptors)
2. Create Pinia stores: catalog, cart, order, locale
3. Create useLocale composable (switching, localStorage, cookie)
4. Create TypeScript interfaces: Category, Product, Cart, Order, Spec

**Files to Create**:
- `resources/js/services/api.ts`
- `resources/js/stores/catalog.ts`, `cart.ts`, `order.ts`, `locale.ts`
- `resources/js/composables/useLocale.ts`
- `resources/js/types/api.ts`

### 5.2 Shared Components & Layouts
**Goal**: Build reusable UI components

**Tasks**:
1. Create ShopLayout (header with logo, nav, locale switcher, cart icon, auth; footer; content slot)
2. Create navigation components: MainNav, CategoryMenu (load from API)
3. Create UI components: ProductCard, CategoryCard, LocaleSwitcher, PriceDisplay
4. Create filter components: FilterSidebar, SpecFilter, PriceRangeFilter
5. Use Tailwind 4 and existing UI patterns

**Files to Create**:
- `resources/js/layouts/ShopLayout.vue`
- `resources/js/components/navigation/MainNav.vue`, `CategoryMenu.vue`
- `resources/js/components/ui/ProductCard.vue`, `CategoryCard.vue`, `LocaleSwitcher.vue`, `PriceDisplay.vue`
- `resources/js/components/catalog/FilterSidebar.vue`, `SpecFilter.vue`, `PriceRangeFilter.vue`

### 5.3 Homepage & Category Pages
**Goal**: Browse categories

**Tasks**:
1. Update Welcome.vue (hero, featured categories grid, popular products)
2. Create HomeController: index() with Inertia (featured data, cached)
3. Create Categories.vue page (display all categories with hierarchy)
4. Create CatalogController: categories(), show($slug)
5. Add routes: GET / → HomeController@index, GET /categories, GET /categories/{slug}

**Files to Create**:
- `app/Http/Controllers/HomeController.php`
- `app/Http/Controllers/CatalogController.php`
- `resources/js/Pages/Catalog/Categories.vue`

**Files to Modify**:
- `resources/js/Pages/Welcome.vue`
- `routes/web.php`

### 5.4 Product Catalog & Search
**Goal**: Browse, filter, search products

**Tasks**:
1. Create Products.vue (filter sidebar, product grid, sorting, pagination with Inertia deferred props)
2. Implement filter state with mergeQuery for URL params, debounced updates
3. Create Search.vue (search input with autocomplete, results grid, filters)
4. Update CatalogController: products(), search() using ProductQueryBuilder
5. Add routes: GET /products, GET /search

**Files to Create**:
- `resources/js/Pages/Catalog/Products.vue`
- `resources/js/Pages/Catalog/Search.vue`

**Files to Modify**:
- `app/Http/Controllers/CatalogController.php`
- `routes/web.php`

### 5.5 Product Detail Page
**Goal**: Show complete product info

**Tasks**:
1. Create ProductDetail.vue (image gallery with zoom, product info, specs table, shop info, quantity selector, add to cart, similar products)
2. Create ProductController: show($id) with related data (nomenclature, shop, category, specs, similar)
3. Add route: GET /products/{id} → ProductController@show

**Files to Create**:
- `resources/js/Pages/Catalog/ProductDetail.vue`
- `app/Http/Controllers/ProductController.php`

### 5.6 Shopping Cart
**Goal**: Manage cart and checkout

**Tasks**:
1. Create Cart/Index.vue (cart items list, quantity controls, cart summary, checkout button, empty state)
2. Implement cart store actions: fetchCart(), addItem(), updateQuantity(), removeItem(), clearCart()
3. Create CartController: index() (show cart page via Inertia)
4. Add route: GET /cart

**Files to Create**:
- `resources/js/Pages/Cart/Index.vue`
- `app/Http/Controllers/CartController.php`

**Files to Modify**:
- `resources/js/stores/cart.ts`
- `routes/web.php`

**Testing**: `tests/Browser/CartFlowTest.php` (Pest Browser test)

### 5.7 Checkout & Orders
**Goal**: Complete order placement

**Tasks**:
1. Create Checkout.vue (delivery form, order summary, submit - auth required)
2. Create Confirmation.vue (order number, thank you, tracking link)
3. Create Orders/Index.vue (user's orders list, status filter, pagination)
4. Create Orders/Show.vue (order details, status timeline, items, cancel button)
5. Create CheckoutController: index(), store() using CreateOrderFromCartAction
6. Create OrderController (web): index(), show() with authorization
7. Add routes (auth): GET /checkout, POST /checkout, GET /orders, GET /orders/{id}, GET /orders/{id}/confirmation

**Files to Create**:
- `resources/js/Pages/Cart/Checkout.vue`
- `resources/js/Pages/Orders/Confirmation.vue`, `Index.vue`, `Show.vue`
- `app/Http/Controllers/CheckoutController.php`
- `app/Http/Controllers/OrderController.php` (web)

**Testing**: `tests/Browser/CheckoutFlowTest.php`

---
