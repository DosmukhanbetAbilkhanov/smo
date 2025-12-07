# 8. Localization Strategy 

## Implementation Approach

### Backend (Laravel)
- Use `lang/ru/`, `lang/kz/`, `lang/en/` directories for translations
- Translation files: `common.php`, `auth.php`, `validation.php` per locale
- SetLocale middleware to detect user preference from cookie/session

### Database
- Store bilingual fields: `name_ru`, `name_kz`, `description_ru`, `description_kz`
- Create helper methods on models: `getLocalizedName()`, `getLocalizedDescription()`

### API
- Return both Russian and Kazakh in API resources
- Let frontend choose which language to display based on user preference
- Example response: `{ "name_ru": "Цемент", "name_kz": "Цемент", ... }`

### Frontend (Vue)
- Locale switcher component in header
- Store preference in localStorage and cookie
- Use composable `useLocale()` to access current locale and switch languages
- Display appropriate field based on current locale

### Filament Panels
- Configure panel locale in PanelProvider
- Support Russian and Kazakh for form labels, table headers, notifications
- Use bilingual form fields with `HasBilingualFields` trait

---