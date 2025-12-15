<script setup lang="ts">
import FilterSidebar from '@/components/catalog/FilterSidebar.vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { PaginatedProducts, ProductFilters } from '@/types/api';
import { router } from '@inertiajs/vue3';
import { ArrowUpDown, ChevronLeft, ChevronRight, Home, Search as SearchIcon, SlidersHorizontal, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    products: PaginatedProducts;
    filters: ProductFilters;
    query?: string;
}

const props = defineProps<Props>();
const { t } = useLocale();

const sortOptions = [
    { value: 'relevance', label: { ru: 'По релевантности', kz: 'Сәйкестік бойынша' } },
    { value: 'price_asc', label: { ru: 'Цена: по возрастанию', kz: 'Бағасы: өсу' } },
    { value: 'price_desc', label: { ru: 'Цена: по убыванию', kz: 'Бағасы: кему' } },
    { value: 'latest', label: { ru: 'Новинки', kz: 'Жаңалықтар' } },
];

const searchQuery = ref(props.query || '');
const currentSort = ref(props.filters.sort_by || 'relevance');
const isLoading = ref(false);
const searchDebounce = ref<number | null>(null);

const currentPage = computed(() => props.products.current_page);
const totalPages = computed(() => props.products.last_page);
const hasNextPage = computed(() => currentPage.value < totalPages.value);
const hasPrevPage = computed(() => currentPage.value > 1);

watch(searchQuery, (newQuery) => {
    if (searchDebounce.value) {
        clearTimeout(searchDebounce.value);
    }

    searchDebounce.value = window.setTimeout(() => {
        if (newQuery.trim()) {
            performSearch(newQuery);
        }
    }, 500);
});

function performSearch(query: string) {
    if (!query.trim()) return;

    isLoading.value = true;

    router.get(
        '/search',
        { q: query, ...props.filters, page: 1 },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'filters', 'query'],
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
}

function clearSearch() {
    searchQuery.value = '';
    router.get('/search');
}

function updateFilters(newFilters: Partial<ProductFilters>) {
    isLoading.value = true;

    router.get(
        '/search',
        { q: searchQuery.value, ...props.filters, ...newFilters, page: 1 },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'filters'],
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
}

function clearFilters() {
    updateFilters({
        min_price: undefined,
        max_price: undefined,
        category_id: undefined,
        sort_by: 'relevance',
    });
}

function updateSort(value: string) {
    currentSort.value = value;
    updateFilters({ sort_by: value });
}

function goToPage(page: number) {
    if (page < 1 || page > totalPages.value) return;

    isLoading.value = true;

    router.get(
        '/search',
        { q: searchQuery.value, ...props.filters, page },
        {
            preserveState: true,
            preserveScroll: false,
            only: ['products'],
            onFinish: () => {
                isLoading.value = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
        },
    );
}
</script>

<template>
    <ShopLayout>
        <!-- Breadcrumb Bar -->
        <div class="breadcrumb-bar">
            <div class="container mx-auto px-4 py-4">
                <Breadcrumb>
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink href="/">
                                <Home :size="16" />
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbPage>
                                {{ t({ ru: 'Поиск', kz: 'Іздеу' }) }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>
            </div>
        </div>

        <!-- Search Page -->
        <div class="search-page bg-pattern">
            <div class="page-container">
                <!-- Search Header -->
                <div class="search-header animate-fadeInUp">
                    <h1 class="page-title">
                        {{ t({ ru: 'Поиск товаров', kz: 'Тауарларды іздеу' }) }}
                    </h1>

                    <!-- Search Input -->
                    <div class="search-input-wrapper">
                        <SearchIcon :size="20" class="search-icon" />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            :placeholder="t({ ru: 'Поиск товаров...', kz: 'Тауарларды іздеу...' })"
                            class="search-input"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="clear-btn"
                        >
                            <X :size="20" />
                        </button>
                    </div>

                    <div v-if="query" class="search-info">
                        <span class="search-query">"{{ query }}"</span>
                        <span class="search-results">
                            {{ t({
                                ru: `${products.total} товаров найдено`,
                                kz: `${products.total} тауар табылды`,
                            }) }}
                        </span>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="catalog-grid">
                    <!-- Filter Sidebar -->
                    <aside class="filter-sidebar animate-fadeInUp" style="animation-delay: 100ms">
                        <div class="filter-header">
                            <SlidersHorizontal :size="20" class="filter-icon" />
                            <h2 class="filter-title">
                                {{ t({ ru: 'Фильтры', kz: 'Сүзгілер' }) }}
                            </h2>
                        </div>
                        <FilterSidebar
                            :filters="filters"
                            @update:filters="updateFilters"
                            @clear="clearFilters"
                        />
                    </aside>

                    <!-- Results Section -->
                    <main class="products-main">
                        <!-- Toolbar -->
                        <div v-if="query && products.total > 0" class="products-toolbar animate-fadeInUp" style="animation-delay: 200ms">
                            <div class="toolbar-info">
                                <span class="results-count">
                                    {{ t({
                                        ru: `${products.from || 0}-${products.to || 0} из ${products.total}`,
                                        kz: `${products.from || 0}-${products.to || 0} ${products.total} ішінде`,
                                    }) }}
                                </span>
                            </div>

                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <button class="sort-button">
                                        <ArrowUpDown :size="18" />
                                        <span>{{ t(sortOptions.find(o => o.value === currentSort)?.label || sortOptions[0].label) }}</span>
                                    </button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem
                                        v-for="option in sortOptions"
                                        :key="option.value"
                                        @click="updateSort(option.value)"
                                        :class="{ 'bg-accent': currentSort === option.value }"
                                    >
                                        {{ t(option.label) }}
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <!-- Loading State -->
                        <div v-if="isLoading" class="products-grid">
                            <div v-for="i in 9" :key="i" class="loading-card">
                                <Skeleton class="skeleton-image" />
                                <Skeleton class="skeleton-title" />
                                <Skeleton class="skeleton-price" />
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div
                            v-else-if="query && products.data.length > 0"
                            class="products-grid"
                        >
                            <div
                                v-for="(product, index) in products.data"
                                :key="product.id"
                                class="animate-fadeInUp"
                                :style="{ animationDelay: `${index * 50}ms` }"
                            >
                                <ProductCard :product="product" />
                            </div>
                        </div>

                        <!-- Empty State - No Query -->
                        <div v-else-if="!query" class="empty-state animate-fadeInUp">
                            <div class="empty-icon">
                                <SearchIcon :size="64" />
                            </div>
                            <h3 class="empty-title">
                                {{ t({ ru: 'Начните поиск', kz: 'Іздеуді бастаңыз' }) }}
                            </h3>
                            <p class="empty-text">
                                {{ t({
                                    ru: 'Введите запрос в поисковую строку выше',
                                    kz: 'Жоғарыдағы іздеу жолына сұрау енгізіңіз',
                                }) }}
                            </p>
                        </div>

                        <!-- Empty State - No Results -->
                        <div v-else class="empty-state animate-fadeInUp">
                            <div class="empty-icon empty-icon-warning">
                                <SearchIcon :size="64" />
                            </div>
                            <h3 class="empty-title">
                                {{ t({ ru: 'Ничего не найдено', kz: 'Ештеңе табылмады' }) }}
                            </h3>
                            <p class="empty-text">
                                {{ t({
                                    ru: 'Попробуйте изменить запрос или фильтры',
                                    kz: 'Сұрауды немесе сүзгілерді өзгертіп көріңіз',
                                }) }}
                            </p>
                            <div class="empty-actions">
                                <button @click="clearSearch" class="btn-secondary-modern">
                                    {{ t({ ru: 'Очистить поиск', kz: 'Іздеуді тазалау' }) }}
                                </button>
                                <button @click="clearFilters" class="btn-secondary-modern">
                                    {{ t({ ru: 'Сбросить фильтры', kz: 'Сүзгілерді тазалау' }) }}
                                </button>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="query && products.data.length > 0 && totalPages > 1"
                            class="pagination animate-fadeInUp"
                        >
                            <button
                                @click="goToPage(currentPage - 1)"
                                :disabled="!hasPrevPage || isLoading"
                                class="pagination-btn"
                            >
                                <ChevronLeft :size="20" />
                            </button>

                            <div class="pagination-pages">
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :disabled="isLoading"
                                    :class="['pagination-page', { active: page === currentPage }]"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <button
                                @click="goToPage(currentPage + 1)"
                                :disabled="!hasNextPage || isLoading"
                                class="pagination-btn"
                            >
                                <ChevronRight :size="20" />
                            </button>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<style scoped>
/* Breadcrumb Bar */
.breadcrumb-bar {
    background: var(--smo-surface);
    border-bottom: 1px solid var(--smo-border);
}

/* Search Page */
.search-page {
    min-height: 100vh;
    background: var(--smo-bg);
}

/* Search Header */
.search-header {
    margin-bottom: 2rem;
}

.search-input-wrapper {
    position: relative;
    max-width: 700px;
    margin: 1.5rem 0;
}

.search-icon {
    position: absolute;
    top: 50%;
    left: 1rem;
    transform: translateY(-50%);
    color: var(--smo-text-muted);
    pointer-events: none;
}

.search-input {
    height: 56px;
    padding-left: 3rem;
    padding-right: 3rem;
    font-family: var(--font-body);
    font-size: 1rem;
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-lg);
    background: var(--smo-surface);
    transition: all var(--transition-base);
}

.search-input:hover {
    border-color: var(--smo-primary-light);
}

.search-input:focus {
    outline: none;
    border-color: var(--smo-primary);
    box-shadow: 0 0 0 3px rgba(44, 95, 93, 0.1);
}

.clear-btn {
    position: absolute;
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: transparent;
    border: none;
    border-radius: var(--radius-sm);
    color: var(--smo-text-muted);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.clear-btn:hover {
    background: var(--smo-bg);
    color: var(--smo-text-primary);
}

.search-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.search-query {
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--smo-primary);
}

.search-results {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--smo-text-secondary);
}

/* Catalog Grid */
.catalog-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 1024px) {
    .catalog-grid {
        grid-template-columns: 280px 1fr;
    }
}

/* Filter Sidebar */
.filter-sidebar {
    background: var(--smo-surface);
    border: 1px solid var(--smo-border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
}

.filter-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--smo-border);
}

.filter-icon {
    color: var(--smo-primary);
}

.filter-title {
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

/* Products Main */
.products-main {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Products Toolbar */
.products-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    background: var(--smo-surface);
    border: 1px solid var(--smo-border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
}

.toolbar-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.results-count {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--smo-text-secondary);
}

.sort-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--smo-surface);
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-sm);
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-text-secondary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.sort-button:hover {
    border-color: var(--smo-primary);
    color: var(--smo-text-primary);
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 640px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Loading Card */
.loading-card {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.skeleton-image {
    aspect-ratio: 1;
    width: 100%;
    border-radius: var(--radius-md);
}

.skeleton-title {
    height: 1rem;
    width: 75%;
    border-radius: var(--radius-sm);
}

.skeleton-price {
    height: 1rem;
    width: 50%;
    border-radius: var(--radius-sm);
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    padding: 3rem 1.5rem;
    background: var(--smo-surface);
    border: 2px dashed var(--smo-border);
    border-radius: var(--radius-lg);
    text-align: center;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 120px;
    height: 120px;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, rgba(44, 95, 93, 0.1) 0%, rgba(44, 95, 93, 0.05) 100%);
    border-radius: 50%;
    color: var(--smo-primary);
}

.empty-icon-warning {
    background: linear-gradient(135deg, rgba(217, 119, 87, 0.1) 0%, rgba(217, 119, 87, 0.05) 100%);
    color: var(--smo-accent);
}

.empty-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--smo-text-primary);
    margin-bottom: 0.5rem;
}

.empty-text {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--smo-text-secondary);
    max-width: 400px;
    margin-bottom: 1.5rem;
}

.empty-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Pagination */
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-top: 2rem;
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--smo-surface);
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-sm);
    color: var(--smo-text-secondary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.pagination-btn:hover:not(:disabled) {
    border-color: var(--smo-primary);
    color: var(--smo-text-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.pagination-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.pagination-pages {
    display: flex;
    gap: 0.5rem;
}

.pagination-page {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 0.75rem;
    background: var(--smo-surface);
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-sm);
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-text-secondary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.pagination-page:hover:not(:disabled) {
    border-color: var(--smo-primary);
    color: var(--smo-text-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.pagination-page.active {
    background: var(--smo-primary);
    border-color: var(--smo-primary);
    color: white;
}

.pagination-page:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .products-toolbar {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .sort-button {
        width: 100%;
        justify-content: center;
    }
}
</style>
