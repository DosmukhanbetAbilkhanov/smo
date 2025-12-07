## PHASE 1: Foundation & Architecture (3-5 days)

### 1.1 Testing Infrastructure Setup
**Goal**: Establish testing foundation before building features

**Tasks**:
1. Create testing structure
   - Run `php artisan make:test --unit Models/UserTest`
   - Run `php artisan make:test --unit Models/CategoryTest`
   - Run `php artisan make:test --unit Models/ProductTest`

2. Configure testing environment
   - Create `.env.testing` with SQLite database
   - Update `phpunit.xml` for proper test database

3. Write model relationship tests
   - Test User→Company relationship
   - Test Category→children/parent relationships
   - Test Product→Shop→Company chain
   - Test Category→Specs pivot relationship

**Files to Create**:
- `tests/Unit/Models/UserTest.php`
- `tests/Unit/Models/CategoryTest.php`
- `tests/Unit/Models/ProductTest.php`
- `.env.testing`

**Testing**: `php artisan test --filter=Models`

### 1.2 Localization Setup
**Goal**: Implement Russian/Kazakh bilingual support

**Tasks**:
1. Create language directories: `lang/ru/`, `lang/kz/`, `lang/en/`
2. Create translation files: `common.php`, `auth.php`, `validation.php` for each locale
3. Update `config/app.php`: Set locale to 'ru', add available_locales
4. Create `SetLocale` middleware for locale detection
5. Create helper for bilingual model fields

**Files to Create**:
- `lang/ru/common.php`, `lang/kz/common.php`
- `app/Http/Middleware/SetLocale.php`
- `app/Support/Helpers/LocalizedAttribute.php`

**Files to Modify**:
- `config/app.php`
- `bootstrap/app.php`

### 1.3 Base Infrastructure
**Goal**: Create shared utilities and contracts

**Tasks**:
1. Create DTOs and Actions directory structure
2. Create UserTypeEnum with constants (Admin=1, RetailCustomer=2, CompanyCustomer=3, Seller=4)
3. Create base UserPolicy with CRUD methods
4. Register policies in AppServiceProvider

**Files to Create**:
- `app/Enums/UserTypeEnum.php`
- `app/Policies/UserPolicy.php`

**Testing**: `tests/Feature/Policies/UserPolicyTest.php`

---