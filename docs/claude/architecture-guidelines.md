# 3. Architecture Guidelines

### 3.1 Backend
- Laravel 12 with modular domain structure
- Thin controllers using Actions, Services, DTOs
- Policies for authorization
- Resources for consistent JSON output

### 3.2 Domain Modules
```
app/Domain/
├── User
├── Company
├── Shop
├── Category
├── Nomenclature
├── Product
├── Specs
├── Cart
└── Order
```

### 3.3 Frontend
```
resources/js/
├── pages/
├── components/
├── stores/
├── api/
├── layouts/
└── router/
```

### 3.4 Filament
- Admin Panel: global platform management
- Seller Panel: shop-level product/stock management

---
