## PHASE 3: Seller Panel (Filament) (5-7 days)

### 3.1 Seller Panel Setup
**Goal**: Create separate Filament panel for sellers

**Tasks**:
1. Create SellerPanelProvider: `php artisan make:filament-panel seller`
2. Configure path `/seller`, check type_id === 4, add Company tenancy
3. Create CheckSellerAccess middleware
4. Create SellerStatsWidget dashboard

**Files to Create**:
- `app/Providers/Filament/SellerPanelProvider.php`
- `app/Http/Middleware/CheckSellerAccess.php`
- `app/Filament/Seller/Widgets/SellerStatsWidget.php`

**Files to Modify**:
- `bootstrap/providers.php` (register SellerPanelProvider)

### 3.2 Shop Management (Seller)
**Goal**: Sellers manage their own shops

**Tasks**:
1. Create Shop resource (seller panel): `php artisan make:filament-resource Shop --panel=seller`
2. Scope queries to current seller's company
3. Add products relationship manager
4. Form fields: name, address, city_id (auto-fill company_id)

**Files to Create**:
- `app/Filament/Seller/Resources/ShopResource.php` (+ 3 pages)
- `app/Filament/Seller/Resources/ShopResource/RelationManagers/ProductsRelationManager.php`

### 3.3 Nomenclature Management (Seller)
**Goal**: Sellers create nomenclatures pending admin approval

**Tasks**:
1. Create Nomenclature resource (seller panel)
2. Form: bilingual names/descriptions, category (leaf only), unit, SKU/GTIN/NTIN, brandname
3. Auto-set status to 'pending' on creation
4. Validate category is leaf (no children)

**Files to Create**:
- `app/Filament/Seller/Resources/NomenclatureResource.php` (+ 3 pages)
- `app/Http/Requests/Seller/StoreNomenclatureRequest.php`

### 3.4 Product Stock Management
**Goal**: Sellers add products with specs and images

**Tasks**:
1. Add migration: images column (JSON) to products table
2. Create Product resource (seller panel)
3. Form: shop, nomenclature, price, quantity, images (multiple upload), specs repeater
4. Auto-populate required specs from category
5. Create RequiredCategorySpecsRule for validation
6. Create SyncProductSpecsAction

**Files to Create**:
- `database/migrations/2025_12_08_000002_add_images_to_products_table.php`
- `app/Filament/Seller/Resources/ProductResource.php` (+ 3 pages)
- `app/Rules/RequiredCategorySpecsRule.php`
- `app/Actions/Product/SyncProductSpecsAction.php`

**Files to Modify**:
- `app/Models/Product.php` (add images casting)

### 3.5 Bulk Operations & CSV Import
**Goal**: Sellers bulk upload products

**Tasks**:
1. Create ImportProducts page: `php artisan make:filament-page ImportProducts --panel=seller`
2. Create ImportProductsAction (parse CSV, validate, create products)
3. Create ProcessProductImport job for large files
4. Show sample CSV format and error reporting

**Files to Create**:
- `app/Filament/Seller/Pages/ImportProducts.php`
- `app/Actions/Product/ImportProductsAction.php`
- `app/Jobs/ProcessProductImport.php`

---
