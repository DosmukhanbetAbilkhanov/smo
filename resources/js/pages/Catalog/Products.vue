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
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Skeleton } from '@/components/ui/skeleton';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { PaginatedProducts, ProductFilters } from '@/types/api';
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Package, SlidersHorizontal } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    products: PaginatedProducts;
    filters: ProductFilters;
}

const props = defineProps<Props>();
const { t } = useLocale();

const sortOptions = [
    { value: 'latest', label: { ru: 'Новинки', kz: 'Жаңалықтар' } },
    { value: 'price_asc', label: { ru: 'Цена: по возрастанию', kz: 'Бағасы: өсу' } },
    { value: 'price_desc', label: { ru: 'Цена: по убыванию', kz: 'Бағасы: кему' } },
    { value: 'popular', label: { ru: 'Популярные', kz: 'Танымал' } },
];

const currentSort = ref(props.filters.sort_by || 'latest');
const isLoading = ref(false);

const currentPage = computed(() => props.products.current_page);
const totalPages = computed(() => props.products.last_page);
const hasNextPage = computed(() => currentPage.value < totalPages.value);
const hasPrevPage = computed(() => currentPage.value > 1);

function updateFilters(newFilters: Partial<ProductFilters>) {
    isLoading.value = true;

    router.get(
        '/products',
        { ...props.filters, ...newFilters, page: 1 },
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
        search: undefined,
        category_id: undefined,
        sort_by: 'latest',
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
        '/products',
        { ...props.filters, page },
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
        <div class="-mx-4 products-page bg-pattern">
            <div class="px-4">
                <!-- Breadcrumbs -->
                <Breadcrumb class="breadcrumb-nav animate-fadeIn">
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink href="/" class="breadcrumb-link">
                                {{ t({ ru: 'Главная', kz: 'Басты бет' }) }}
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbPage class="breadcrumb-current">
                                {{ t({ ru: 'Каталог товаров', kz: 'Тауарлар каталогы' }) }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>

                <!-- Page Header -->
                <div class="page-header animate-fadeInUp">
                    <div class="header-content">
                        <h1 class="page-title">
                            {{ t({ ru: 'Каталог товаров', kz: 'Тауарлар каталогы' }) }}
                        </h1>
                        <div class="results-badge">
                            <Package :size="16" />
                            <span>{{ products.total }} {{ t({ ru: 'товаров', kz: 'тауар' }) }}</span>
                        </div>
                    </div>
                </div>

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

                    <!-- Products Grid -->
                    <main class="products-main">
                        <!-- Sorting and Results Count -->
                        <div class="toolbar animate-fadeInUp" style="animation-delay: 200ms">
                            <span class="results-count">
                                {{ t({
                                    ru: `Показано ${products.from || 0}-${products.to || 0} из ${products.total}`,
                                    kz: `Көрсетілген ${products.from || 0}-${products.to || 0} ${products.total} ішінде`,
                                }) }}
                            </span>

                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <button class="sort-button">
                                        <SlidersHorizontal :size="16" />
                                        {{ t({
                                            ru: 'Сортировка',
                                            kz: 'Сұрыптау',
                                        }) }}:
                                        {{ t(sortOptions.find(o => o.value === currentSort)?.label || sortOptions[0].label) }}
                                    </button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" class="sort-menu">
                                    <DropdownMenuItem
                                        v-for="option in sortOptions"
                                        :key="option.value"
                                        @click="updateSort(option.value)"
                                        :class="{ 'active-sort': currentSort === option.value }"
                                    >
                                        {{ t(option.label) }}
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <!-- Loading State -->
                        <div v-if="isLoading" class="products-grid">
                            <div v-for="i in 9" :key="i" class="skeleton-card">
                                <Skeleton class="skeleton-image" />
                                <Skeleton class="skeleton-title" />
                                <Skeleton class="skeleton-price" />
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div
                            v-else-if="products.data.length > 0"
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

                        <!-- Empty State -->
                        <div v-else class="empty-state">
                            <div class="empty-icon">
                                <Package :size="64" />
                            </div>
                            <h3 class="empty-title">
                                {{ t({ ru: 'Товары не найдены', kz: 'Тауарлар табылмады' }) }}
                            </h3>
                            <p class="empty-subtitle">
                                {{ t({
                                    ru: 'Попробуйте изменить фильтры или параметры поиска',
                                    kz: 'Сүзгілерді немесе іздеу параметрлерін өзгертіп көріңіз',
                                }) }}
                            </p>
                            <Button @click="clearFilters" class="btn-secondary-modern">
                                {{ t({ ru: 'Сбросить фильтры', kz: 'Сүзгілерді тазалау' }) }}
                            </Button>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="products.data.length > 0 && totalPages > 1"
                            class="pagination"
                        >
                            <button
                                @click="goToPage(currentPage - 1)"
                                :disabled="!hasPrevPage || isLoading"
                                class="pagination-btn"
                            >
                                <ChevronLeft :size="16" />
                            </button>

                            <div class="pagination-pages">
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="['pagination-page', { 'active': page === currentPage }]"
                                    :disabled="isLoading"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <button
                                @click="goToPage(currentPage + 1)"
                                :disabled="!hasNextPage || isLoading"
                                class="pagination-btn"
                            >
                                <ChevronRight :size="16" />
                            </button>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<style scoped>
/* Products Page */
.products-page {
    background: var(--smo-bg);
    min-height: 100vh;
    font-family: var(--font-body);
    padding-bottom: 4rem;
}

/* Breadcrumbs */
.breadcrumb-nav {
    margin-bottom: 2rem;
}

.breadcrumb-link {
    font-family: var(--font-body);
    color: var(--smo-text-secondary);
    transition: color var(--transition-base);
}

.breadcrumb-link:hover {
    color: var(--smo-primary);
}

.breadcrumb-current {
    font-family: var(--font-body);
    color: var(--smo-text-primary);
    font-weight: 600;
}

/* Page Header */
.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.results-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.1) 0%,
        rgba(44, 95, 93, 0.05) 100%);
    border: 1px solid rgba(44, 95, 93, 0.2);
    border-radius: var(--radius-lg);
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-primary);
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
        gap: 3rem;
    }
}

/* Filter Sidebar */
.filter-sidebar {
    background: var(--smo-surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--smo-border);
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
}

.filter-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--smo-border);
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
    gap: 2rem;
}

/* Toolbar */
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    background: var(--smo-surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--smo-border);
    box-shadow: var(--shadow-sm);
}

.results-count {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--smo-text-secondary);
    font-weight: 500;
}

.sort-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: var(--smo-bg);
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-text-primary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.sort-button:hover {
    border-color: var(--smo-primary);
    background: var(--smo-surface);
}

.sort-menu {
    min-width: 200px;
}

.active-sort {
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.1) 0%,
        rgba(44, 95, 93, 0.05) 100%);
    color: var(--smo-primary);
    font-weight: 600;
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

/* Loading Skeleton */
.skeleton-card {
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
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 128px;
    height: 128px;
    border-radius: 50%;
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.1) 0%,
        rgba(44, 95, 93, 0.05) 100%);
    color: var(--smo-primary);
    margin-bottom: 1.5rem;
}

.empty-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--smo-text-primary);
    margin-bottom: 0.5rem;
}

.empty-subtitle {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--smo-text-secondary);
    margin-bottom: 1.5rem;
    max-width: 500px;
}

/* Pagination */
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
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
    border-radius: var(--radius-md);
    color: var(--smo-text-primary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.pagination-btn:hover:not(:disabled) {
    border-color: var(--smo-primary);
    color: var(--smo-primary);
    background: var(--smo-bg);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-pages {
    display: flex;
    gap: 0.25rem;
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
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-text-primary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.pagination-page:hover:not(:disabled) {
    border-color: var(--smo-primary-light);
    color: var(--smo-primary);
}

.pagination-page.active {
    background: linear-gradient(135deg, var(--smo-primary) 0%, var(--smo-primary-light) 100%);
    border-color: var(--smo-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(44, 95, 93, 0.3);
}

.pagination-page:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 1024px) {
    .filter-sidebar {
        order: 2;
    }

    .products-main {
        order: 1;
    }
}

@media (max-width: 640px) {
    .toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .sort-button {
        justify-content: center;
    }
}
</style>
