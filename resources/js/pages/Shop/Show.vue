<script setup lang="ts">
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
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category, PaginatedProducts, Shop } from '@/types/api';
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Grid, MapPin, Package, Search, Store, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    shop: Shop;
    products: PaginatedProducts;
    categories: Category[];
    searchQuery?: string;
    selectedCategoryId?: number;
}

const props = defineProps<Props>();
const { t, getLocalizedName } = useLocale();

const isLoading = ref(false);
const localSearchQuery = ref(props.searchQuery || '');
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const currentPage = computed(() => props.products.current_page);
const totalPages = computed(() => props.products.last_page);
const hasNextPage = computed(() => currentPage.value < totalPages.value);
const hasPrevPage = computed(() => currentPage.value > 1);

// Live search with debounce
watch(localSearchQuery, (newValue, oldValue) => {
    if (newValue === props.searchQuery) return;

    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        isLoading.value = true;

        router.get(
            `/shops/${props.shop.id}`,
            {
                search: newValue || undefined,
                category_id: props.selectedCategoryId,
            },
            {
                preserveState: true,
                preserveScroll: true,
                only: ['products', 'searchQuery', 'categories'],
                onFinish: () => {
                    isLoading.value = false;
                },
            },
        );
    }, 500);
});

function goToPage(page: number) {
    if (page < 1 || page > totalPages.value) return;

    isLoading.value = true;

    router.get(
        `/shops/${props.shop.id}`,
        {
            page,
            search: props.searchQuery,
            category_id: props.selectedCategoryId,
        },
        {
            preserveState: true,
            preserveScroll: false,
            only: ['products', 'categories'],
            onFinish: () => {
                isLoading.value = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
        },
    );
}

function clearSearch() {
    localSearchQuery.value = '';
    // The watch will handle the search automatically
}

function filterByCategory(categoryId: number | null) {
    if (categoryId === props.selectedCategoryId) return;

    isLoading.value = true;

    router.get(
        `/shops/${props.shop.id}`,
        {
            search: props.searchQuery,
            category_id: categoryId || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'selectedCategoryId', 'categories'],
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
}
</script>

<template>
    <ShopLayout>
        <div class="shop-page bg-pattern">
            <div class="page-container">
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
                                {{ shop.name }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>

                <!-- Shop Header -->
                <div class="shop-header animate-fadeInUp">
                    <div class="shop-icon-wrapper">
                        <Store :size="32" class="shop-icon" />
                    </div>
                    <div class="shop-info">
                        <h1 class="shop-name">{{ shop.name }}</h1>
                        <div class="shop-meta">
                            <div v-if="shop.city" class="meta-item">
                                <MapPin :size="16" />
                                <span>{{ shop.city.name_ru }}</span>
                            </div>
                            <div class="meta-item">
                                <Package :size="16" />
                                <span>{{ products.total }} {{ t({ ru: 'товаров', kz: 'тауар' }) }}</span>
                            </div>
                        </div>
                        <p v-if="shop.address" class="shop-address">{{ shop.address }}</p>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="search-section animate-fadeInUp" style="animation-delay: 100ms">
                    <div class="search-input-wrapper">
                        <Search :size="16" class="search-icon" />
                        <Input
                            v-model="localSearchQuery"
                            type="text"
                            :placeholder="t({ ru: 'Поиск товаров...', kz: 'Тауарларды іздеу...' })"
                            class="search-input"
                        />
                        <button
                            v-if="localSearchQuery"
                            @click="clearSearch"
                            class="search-clear-btn"
                        >
                            <X :size="16" />
                        </button>
                    </div>
                </div>

                <!-- Categories Navigation -->
                <div
                    v-if="categories.length > 0"
                    class="categories-section animate-fadeInUp"
                    style="animation-delay: 200ms"
                >
                    <div class="categories-header">
                        <Grid :size="20" class="categories-icon" />
                        <h2 class="categories-title">
                            {{ t({ ru: 'Категории', kz: 'Санаттар' }) }}
                        </h2>
                    </div>
                    <div class="categories-nav">
                        <button
                            @click="filterByCategory(null)"
                            :class="['category-btn', { active: !selectedCategoryId }]"
                            :disabled="isLoading"
                        >
                            {{ t({ ru: 'Все товары', kz: 'Барлық тауарлар' }) }}
                        </button>
                        <template v-for="category in categories" :key="category.id">
                            <button
                                @click="filterByCategory(category.id)"
                                :class="['category-btn', { active: selectedCategoryId === category.id }]"
                                :disabled="isLoading"
                            >
                                {{ getLocalizedName(category) }}
                            </button>
                            <button
                                v-for="child in category.children"
                                :key="child.id"
                                @click="filterByCategory(child.id)"
                                :class="['category-btn', 'category-btn-child', { active: selectedCategoryId === child.id }]"
                                :disabled="isLoading"
                            >
                                {{ getLocalizedName(child) }}
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="products-section">
                    <div class="section-header animate-fadeInUp" style="animation-delay: 300ms">
                        <h2 class="section-title">
                            {{ t({ ru: 'Товары магазина', kz: 'Дүкен тауарлары' }) }}
                        </h2>
                        <div class="results-badge">
                            <Package :size="16" />
                            <span>{{ products.total }} {{ t({ ru: 'товаров', kz: 'тауар' }) }}</span>
                        </div>
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
                        class="products-grid animate-fadeInUp"
                        style="animation-delay: 400ms"
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
                            {{ t({ ru: 'В магазине пока нет товаров', kz: 'Дүкенде әлі тауарлар жоқ' }) }}
                        </h3>
                        <p class="empty-subtitle">
                            {{ t({
                                ru: 'Проверьте позже или посмотрите другие магазины',
                                kz: 'Кейінірек тексеріңіз немесе басқа дүкендерді қараңыз',
                            }) }}
                        </p>
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
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<style scoped>
/* Shop Page */
.shop-page {
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

/* Shop Header */
.shop-header {
    display: flex;
    gap: 1.5rem;
    padding: 2rem;
    background: var(--smo-surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--smo-border);
    box-shadow: var(--shadow-lg);
    margin-bottom: 3rem;
}

.shop-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    flex-shrink: 0;
    border-radius: var(--radius-lg);
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.1) 0%,
        rgba(44, 95, 93, 0.05) 100%);
    border: 2px solid rgba(44, 95, 93, 0.2);
}

.shop-icon {
    color: var(--smo-primary);
}

.shop-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.shop-name {
    font-family: var(--font-display);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.shop-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--smo-text-secondary);
}

.meta-item svg {
    color: var(--smo-primary);
}

.shop-address {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--smo-text-secondary);
}

/* Search Section */
.search-section {
    margin-bottom: 1.5rem;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: var(--smo-surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--smo-border);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
}

.search-input-wrapper:focus-within {
    border-color: var(--smo-primary);
    box-shadow: 0 0 0 2px rgba(44, 95, 93, 0.1);
}

.search-icon {
    color: var(--smo-text-secondary);
    flex-shrink: 0;
}

.search-input {
    flex: 1;
    border: none;
    background: transparent;
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--smo-text-primary);
    outline: none;
    padding: 0;
}

.search-input::placeholder {
    color: var(--smo-text-muted);
}

.search-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: var(--radius-sm);
    border: none;
    background: var(--smo-bg);
    color: var(--smo-text-muted);
    cursor: pointer;
    transition: all var(--transition-base);
    flex-shrink: 0;
}

.search-clear-btn:hover {
    background: var(--smo-border);
    color: var(--smo-text-primary);
}

/* Categories Section */
.categories-section {
    background: var(--smo-surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--smo-border);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.categories-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--smo-border);
}

.categories-icon {
    color: var(--smo-primary);
}

.categories-title {
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.categories-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.category-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.875rem;
    background: var(--smo-bg);
    border: 1px solid var(--smo-border);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--smo-text-primary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.category-btn:hover:not(:disabled) {
    border-color: var(--smo-primary);
    color: var(--smo-primary);
    background: rgba(44, 95, 93, 0.05);
}

.category-btn.active {
    background: var(--smo-primary);
    border-color: var(--smo-primary);
    color: white;
}

.category-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.category-btn-child {
    padding-left: 1.25rem;
    font-weight: 400;
    font-size: 0.8125rem;
}

/* Products Section */
.products-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.section-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--smo-text-primary);
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

@media (min-width: 1280px) {
    .products-grid {
        grid-template-columns: repeat(4, 1fr);
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
@media (max-width: 640px) {
    .shop-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .shop-meta {
        justify-content: center;
    }

    .section-header {
        flex-direction: column;
        align-items: stretch;
    }

    .results-badge {
        justify-content: center;
    }

    .categories-section {
        padding: 1rem;
    }

    .category-btn {
        font-size: 0.8125rem;
        padding: 0.5rem 0.75rem;
        font-weight: 700;
    }

    .category-btn-child {
        padding-left: 1rem;
        font-size: 0.75rem;
        font-weight: 400;
    }
}
</style>
