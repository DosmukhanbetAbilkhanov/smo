## PHASE 6: Polish & Optimization (5-7 days)

### 6.1 Performance Optimization
**Goal**: Optimize queries and caching

**Tasks**:
1. Add migration: indexes on products(shop_id, nomenclature_id), product_specs(product_id, spec_name), categories(parent_id, slug), orders(user_id, status)
2. Add eager loading to all controllers, check N+1 queries
3. Implement caching: categories (1h), nomenclatures (30m), product filters (15m)
4. Create WarmCache command, schedule daily

**Files to Create**:
- `database/migrations/2025_12_08_000010_add_indexes_for_performance.php`
- `app/Console/Commands/WarmCache.php`

### 6.2 Search Enhancement (Optional)
**Goal**: Improve search quality

**Tasks**:
1. Install Laravel Scout: `composer require laravel/scout`
2. Add searchable to Product model (implement toSearchableArray())
3. Update ProductSearchService to use Scout with database fallback

### 6.3 Image Optimization
**Goal**: Optimize uploads

**Tasks**:
1. Install intervention/image: `composer require intervention/image`
2. Create ProcessProductImageAction (resize 800x800, generate thumbnails 200x200, optimize)
3. Update Product resource FileUpload to use action

**Files to Create**:
- `app/Actions/Product/ProcessProductImageAction.php`

### 6.4 Notifications & Emails
**Goal**: Send order confirmations

**Tasks**:
1. Create OrderPlacedNotification (queue email to customer)
2. Create NewOrderForSellerNotification (notify seller)
3. Create OrderConfirmationMail with bilingual Blade template
4. Trigger in CreateOrderFromCartAction

**Files to Create**:
- `app/Notifications/OrderPlacedNotification.php`, `NewOrderForSellerNotification.php`
- `app/Mail/OrderConfirmationMail.php`
- `resources/views/emails/orders/confirmation.blade.php`

**Testing**: Use Mailhog/Mailtrap, `tests/Feature/Notifications/OrderNotificationTest.php`

### 6.5 Security & Validation
**Goal**: Robust security

**Tasks**:
1. Add rate limiting to API routes (60/min general, 30/min search, 5/min orders)
2. Update CORS configuration
3. Create ProductPolicy and OrderPolicy with authorization checks
4. Review all Form Requests, add sanitization
5. Ensure CSRF for Inertia

**Files to Create**:
- `app/Policies/ProductPolicy.php`, `OrderPolicy.php`

**Files to Modify**:
- `routes/api.php` (rate limiting), `config/cors.php`

### 6.6 Testing Suite Completion
**Goal**: >80% coverage

**Tasks**:
1. Complete unit tests (models, actions, services, rules)
2. Complete feature tests (API, Filament, web routes)
3. Add browser tests (Pest v4): full user flows, seller flows, admin flows
4. Run: `php artisan test --coverage --min=80`

### 6.7 Documentation
**Goal**: Complete docs

**Tasks**:
1. Create API documentation: `docs/api/v1/README.md` (all endpoints with examples)
2. Create deployment guide: `docs/deployment.md` (requirements, env vars, migration steps)
3. Create developer guide: `docs/development.md` (structure, conventions, adding features)
4. Update README.md (overview, installation, testing, tech stack)

---