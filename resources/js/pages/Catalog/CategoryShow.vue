<script setup lang="ts">
import CategoryCard from '@/components/shop/CategoryCard.vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category, PaginatedResponse, Product } from '@/types/api';
import { Head, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, FolderTree, Home, Package } from 'lucide-vue-next';

interface Props {
    category: Category;
    products: PaginatedResponse<Product>;
}

const props = defineProps<Props>();
const { getLocalizedName, getLocalizedDescription, t } = useLocale();

const categoryName = getLocalizedName(props.category);
const categoryDescription = getLocalizedDescription(props.category);

function goToPage(page: number) {
    router.get(
        `/categories/${props.category.slug}`,
        { page },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <Head :title="categoryName" />

    <ShopLayout>
        <!-- Breadcrumb -->
        <div class="breadcrumb-bar">
            <div class="container mx-auto px-4 py-4">
                <Breadcrumb>
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink as-child>
                                <Link href="/">
                                    <Home :size="16" />
                                </Link>
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbLink as-child>
                                <Link href="/categories">
                                    {{ t({ ru: 'Категории', kz: 'Санаттар' }) }}
                                </Link>
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbPage>
                                {{ categoryName }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>
            </div>
        </div>

        <!-- Category Page -->
        <div class="category-page bg-pattern">
            <!-- Category Header -->
            <div class="category-header">
                <div class="container mx-auto px-4">
                    <div class="header-content animate-fadeInUp">
                        <h1 class="category-title">{{ categoryName }}</h1>
                        <p v-if="categoryDescription" class="category-description">
                            {{ categoryDescription }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Subcategories Section -->
            <div v-if="category.children && category.children.length > 0" class="subcategories-section">
                <div class="container mx-auto px-4">
                    <div class="section-header animate-fadeInUp">
                        <FolderTree :size="24" class="section-icon" />
                        <h2 class="section-title">
                            {{ t({ ru: 'Подкатегории', kz: 'Санаттар' }) }}
                        </h2>
                    </div>
                    <div class="subcategories-grid">
                        <div
                            v-for="(child, index) in category.children"
                            :key="child.id"
                            class="animate-fadeInUp"
                            :style="{ animationDelay: `${index * 50}ms` }"
                        >
                            <CategoryCard :category="child" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="products-section">
                <div class="container mx-auto px-4">
                    <div class="section-header animate-fadeInUp">
                        <div class="header-left">
                            <Package :size="24" class="section-icon" />
                            <h2 class="section-title">
                                {{ t({ ru: 'Товары', kz: 'Өнімдер' }) }}
                            </h2>
                        </div>
                        <div class="products-badge">
                            <span>{{ products.total }} {{ t({ ru: 'товаров', kz: 'тауар' }) }}</span>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-if="products.data.length > 0" class="products-grid">
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
                    <div v-else class="empty-state animate-fadeInUp">
                        <div class="empty-icon">
                            <Package :size="64" />
                        </div>
                        <h3 class="empty-title">
                            {{ t({ ru: 'Товары не найдены', kz: 'Өнімдер табылмады' }) }}
                        </h3>
                        <p class="empty-text">
                            {{
                                t({
                                    ru: 'В этой категории пока нет товаров',
                                    kz: 'Бұл санатта әзірше өнімдер жоқ',
                                })
                            }}
                        </p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="pagination animate-fadeInUp">
                        <button
                            class="pagination-btn"
                            :disabled="products.current_page === 1"
                            @click="goToPage(products.current_page - 1)"
                        >
                            <ChevronLeft :size="20" />
                            <span>{{ t({ ru: 'Предыдущая', kz: 'Алдыңғы' }) }}</span>
                        </button>

                        <div class="pagination-info">
                            {{
                                t({
                                    ru: `Страница ${products.current_page} из ${products.last_page}`,
                                    kz: `${products.current_page} бет ${products.last_page}`,
                                })
                            }}
                        </div>

                        <button
                            class="pagination-btn"
                            :disabled="products.current_page === products.last_page"
                            @click="goToPage(products.current_page + 1)"
                        >
                            <span>{{ t({ ru: 'Следующая', kz: 'Келесі' }) }}</span>
                            <ChevronRight :size="20" />
                        </button>
                    </div>
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

/* Category Page */
.category-page {
    min-height: 100vh;
    background: var(--smo-bg);
}

/* Category Header */
.category-header {
    background: linear-gradient(135deg, rgba(44, 95, 93, 0.05) 0%, rgba(44, 95, 93, 0.02) 100%);
    border-bottom: 1px solid var(--smo-border);
    padding: 3rem 0;
}

.header-content {
    max-width: 800px;
}

.category-title {
    font-family: var(--font-display);
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--smo-text-primary);
    line-height: 1.2;
    letter-spacing: -0.02em;
    margin-bottom: 0.75rem;
}

.category-description {
    font-family: var(--font-body);
    font-size: 1.125rem;
    color: var(--smo-text-secondary);
    line-height: 1.6;
}

/* Section Header */
.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.section-icon {
    color: var(--smo-primary);
}

.section-title {
    font-family: var(--font-display);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.products-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, rgba(44, 95, 93, 0.1) 0%, rgba(44, 95, 93, 0.05) 100%);
    border: 1px solid rgba(44, 95, 93, 0.2);
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-primary);
}

/* Subcategories Section */
.subcategories-section {
    padding: 3rem 0;
    border-bottom: 1px solid var(--smo-border);
}

.subcategories-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 640px) {
    .subcategories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .subcategories-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Products Section */
.products-section {
    padding: 3rem 0;
}

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
}

/* Pagination */
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-top: 3rem;
}

.pagination-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--smo-surface);
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-text-secondary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.pagination-btn:hover:not(:disabled) {
    border-color: var(--smo-primary);
    color: var(--smo-text-primary);
    background: var(--smo-bg);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.pagination-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.pagination-info {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--smo-text-secondary);
    padding: 0.75rem 1rem;
}

@media (max-width: 640px) {
    .category-title {
        font-size: 2rem;
    }

    .section-title {
        font-size: 1.5rem;
    }

    .pagination-btn span {
        display: none;
    }
}
</style>
