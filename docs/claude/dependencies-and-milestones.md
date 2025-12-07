# 9. Dependencies & Milestones

## Phase Dependencies

```
Phase 1 (Foundation)
    ↓
Phase 2 (Admin Panel) ← depends on Phase 1.1, 1.2
    ↓
Phase 3 (Seller Panel) ← depends on Phase 2 (for nomenclature approval)
    ↓
Phase 4 (API Backend) ← depends on Phase 2.3 (Categories), Phase 3.4 (Products)
    ↓
Phase 5 (Frontend) ← depends on Phase 4 (API endpoints)
    ↓
Phase 6 (Polish) ← can run parallel with Phase 5
```

## Critical Milestones

### Week 1: Foundation Complete
- **Deliverables**: Testing infrastructure, localization, policies
- **Validation**: `php artisan test --filter=Models` passes
- **Blockers**: None

### Week 2: Admin Panel Complete
- **Deliverables**: All Filament resources (City, Unit, Category, Spec, User, Company, Nomenclature)
- **Validation**: Admin can manage all reference data via `/admin` panel
- **Blockers**: Requires Week 1 completion

### Week 3: Seller Panel Complete
- **Deliverables**: Seller panel with Shop, Nomenclature, Product resources
- **Validation**: Seller can create products with specs via `/seller` panel
- **Blockers**: Requires Admin panel for nomenclature approval

### Week 4-5: API Complete
- **Deliverables**: All API endpoints (Categories, Products, Cart, Orders)
- **Validation**: API tests pass, Postman/curl testing successful
- **Blockers**: Requires Products with specs from Seller panel

### Week 6-7: Frontend Complete
- **Deliverables**: Customer-facing SPA (catalog, cart, checkout, orders)
- **Validation**: Browser tests pass, full e2e flow works
- **Blockers**: Requires API endpoints

### Week 8: Production Ready
- **Deliverables**: Performance optimization, security hardening, documentation, >80% test coverage
- **Validation**: All tests pass, documentation complete
- **Blockers**: None

---