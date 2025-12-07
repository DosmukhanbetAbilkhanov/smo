# 7. Testing Strategy

## Unit Testing
Test individual components in isolation:
- **Models**: Relationships, accessors, mutators, scopes
- **Actions**: Business logic without external dependencies
- **Services**: Filtering algorithms, search logic
- **Rules**: Custom validation rules
- **Observers**: Model event side effects

## Feature Testing
Test integrated functionality:
- **API Endpoints**: All routes with various scenarios (success, validation errors, unauthorized)
- **Filament Resources**: CRUD operations, authorization, filters, bulk actions
- **Web Routes**: Page rendering, redirects, data passing
- **Cart Operations**: Add, update, remove, clear
- **Order Lifecycle**: Creation, cancellation, status changes

## Browser Testing (Pest v4)
Test complete user workflows in real browsers:

**Customer Flows**:
- Browse categories → View product → Add to cart → Checkout → View order
- Search products → Apply filters → Sort results
- Register account → Login → Update profile

**Seller Flows**:
- Login → Create shop → Add nomenclature → Add product with specs → Upload images

**Admin Flows**:
- Login → Manage categories → Attach specs → Approve nomenclature

## Performance Testing
- Test with 10,000+ products to ensure scalability
- Verify query count <10 per page using Laravel Debugbar
- Test concurrent users (optional, use Apache Bench)