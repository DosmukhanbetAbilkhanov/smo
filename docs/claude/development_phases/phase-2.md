## PHASE 2: Admin Panel (Filament) (5-7 days)

### 2.1 Admin Panel Foundation
**Goal**: Setup admin-only Filament resources with authorization

**Tasks**:
1. Update AdminPanelProvider with authGuard and authorization middleware
2. Create CheckUserType middleware (check type_id === 1)
3. Create StatsOverview widget (Total Products, Sellers, Categories)

**Files to Create**:
- `app/Http/Middleware/CheckUserType.php`
- `app/Filament/Widgets/StatsOverview.php`

**Files to Modify**:
- `app/Providers/Filament/AdminPanelProvider.php`

### 2.2 Reference Data Resources
**Goal**: Admin manages cities and units

**Tasks**:
1. Create City resource: `php artisan make:filament-resource City --panel=admin --generate`
2. Create Unit resource: `php artisan make:filament-resource Unit --panel=admin --generate`
3. Create bilingual form trait for reusable bilingual fields
4. Configure forms, tables, search, navigation groups

**Files to Create**:
- `app/Filament/Resources/CityResource.php` (+ 3 pages)
- `app/Filament/Resources/UnitResource.php` (+ 3 pages)
- `app/Filament/Concerns/HasBilingualFields.php`

### 2.3 Category & Specification Management
**Goal**: Admin manages hierarchical categories and specifications

**Tasks**:
1. Create Spec resource with bilingual fields
2. Create Category resource with parent_id hierarchy, auto-generated slugs, icon upload
3. Create CategorySpec relationship manager (attach/detach specs, is_required toggle)
4. Create CategoryObserver for automatic slug generation from name_ru

**Files to Create**:
- `app/Filament/Resources/SpecResource.php` (+ 3 pages)
- `app/Filament/Resources/CategoryResource.php` (+ 3 pages)
- `app/Filament/Resources/CategoryResource/RelationManagers/SpecsRelationManager.php`
- `app/Observers/CategoryObserver.php`

**Files to Modify**:
- `app/Providers/AppServiceProvider.php` (register observer)

### 2.4 User & Company Management
**Goal**: Admin manages sellers and their companies

**Tasks**:
1. Create User resource with type, city, email, phone, active toggle
2. Create Company resource with owner (user_id), city, shops relationship
3. Create Shop relationship manager for Company
4. Add filters, bulk actions (activate/deactivate)

**Files to Create**:
- `app/Filament/Resources/UserResource.php` (+ 3 pages)
- `app/Filament/Resources/CompanyResource.php` (+ 3 pages)
- `app/Filament/Resources/CompanyResource/RelationManagers/ShopsRelationManager.php`

### 2.5 Nomenclature Management (Admin)
**Goal**: Admin approves/rejects nomenclatures

**Tasks**:
1. Add migration: status column (draft/pending/approved/rejected), approved_by, approved_at
2. Create Nomenclature resource (read-only forms for admin)
3. Create Approve/Reject actions with bulk support
4. Add status filters and badges

**Files to Create**:
- `database/migrations/2025_12_08_000001_add_status_to_nomenclatures_table.php`
- `app/Filament/Resources/NomenclatureResource.php` (+ 3 pages)
- `app/Filament/Resources/NomenclatureResource/Actions/ApproveAction.php`

**Files to Modify**:
- `app/Models/Nomenclature.php` (add status enum)

---
