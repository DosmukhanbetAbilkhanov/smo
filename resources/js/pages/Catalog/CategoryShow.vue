<script setup lang="ts">
import CategoryCard from '@/components/shop/CategoryCard.vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import PageBreadcrumb from '@/components/PageBreadcrumb.vue';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category, PaginatedResponse, Product } from '@/types/api';
import { Head, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, FolderTree, Home, Package } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    category: Category;
    products: PaginatedResponse<Product>;
}

const props = defineProps<Props>();
const { getLocalizedName, getLocalizedDescription, t } = useLocale();

const categoryName = getLocalizedName(props.category);
const categoryDescription = getLocalizedDescription(props.category);

const breadcrumbItems = computed(() => [
    { icon: Home, href: '/' },
    { label: t({ ru: 'Категории', kz: 'Санаттар' }), href: '/categories' },
    { label: categoryName, isCurrentPage: true },
]);

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
        <PageBreadcrumb :items="breadcrumbItems" />

        <!-- Category Page -->
        <div class="-mx-4 min-h-screen bg-[var(--smo-bg)] bg-pattern">
            <!-- Category Header -->
            <div class="px-4 py-4">
                <h1 class="text-lg font-bold">{{ categoryName }}</h1>
            </div>

            <!-- Subcategories Section -->
            <div v-if="category.children && category.children.length > 0" class="py-4">
                <div class="px-4">
                    <div class="flex items-center gap-2 mb-4">
                        <h2 class="text-normal font-semibold text-slate-400">
                            {{ t({ ru: 'Подкатегории', kz: 'Санаттар' }) }}
                        </h2>
                    </div>
                    <div class="flex space-x-2">
                        <div
                            v-for="(child, index) in category.children"
                            :key="child.id"
                        >
                            <CategoryCard :category="child" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="pt-4 pb-12">
                <div class="px-4">
                    <div class="flex items-center gap-3 mb-8 animate-fadeInUp">
                        <div class="flex items-center gap-3 flex-1">
                            <h2 class="font-bold text-lg">
                                {{ t({ ru: 'Товары', kz: 'Өнімдер' }) }}
                            </h2>
                        </div>
                        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] border border-[rgba(44,95,93,0.2)] rounded-[var(--radius-md)] font-[var(--font-display)] text-sm font-semibold text-[var(--smo-primary)]">
                            <span>{{ products.total }} {{ t({ ru: 'товаров', kz: 'тауар' }) }}</span>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                    <div v-else class="flex flex-col items-center justify-center min-h-[400px] px-12 py-12 bg-[var(--smo-surface)] border-2 border-dashed border-[var(--smo-border)] rounded-[var(--radius-lg)] text-center animate-fadeInUp">
                        <div class="flex items-center justify-center w-[120px] h-[120px] mb-6 bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] rounded-full text-[var(--smo-primary)]">
                            <Package :size="64" />
                        </div>
                        <h3 class="font-[var(--font-display)] text-2xl font-bold text-[var(--smo-text-primary)] mb-2">
                            {{ t({ ru: 'Товары не найдены', kz: 'Өнімдер табылмады' }) }}
                        </h3>
                        <p class="font-[var(--font-body)] text-base text-[var(--smo-text-secondary)] max-w-[400px]">
                            {{
                                t({
                                    ru: 'В этой категории пока нет товаров',
                                    kz: 'Бұл санатта әзірше өнімдер жоқ',
                                })
                            }}
                        </p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="flex items-center justify-center gap-4 mt-12 animate-fadeInUp">
                        <button
                            class="flex items-center gap-2 px-6 py-3 bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-md)] font-[var(--font-display)] text-[0.9375rem] font-semibold text-[var(--smo-text-secondary)] cursor-pointer transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-text-primary)] hover:bg-[var(--smo-bg)] hover:-translate-y-0.5 hover:shadow-md disabled:opacity-40 disabled:cursor-not-allowed"
                            :disabled="products.current_page === 1"
                            @click="goToPage(products.current_page - 1)"
                        >
                            <ChevronLeft :size="20" />
                            <span class="hidden sm:inline">{{ t({ ru: 'Предыдущая', kz: 'Алдыңғы' }) }}</span>
                        </button>

                        <div class="font-[var(--font-body)] text-[0.9375rem] font-medium text-[var(--smo-text-secondary)] px-4 py-3">
                            {{
                                t({
                                    ru: `Страница ${products.current_page} из ${products.last_page}`,
                                    kz: `${products.current_page} бет ${products.last_page}`,
                                })
                            }}
                        </div>

                        <button
                            class="flex items-center gap-2 px-6 py-3 bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-md)] font-[var(--font-display)] text-[0.9375rem] font-semibold text-[var(--smo-text-secondary)] cursor-pointer transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-text-primary)] hover:bg-[var(--smo-bg)] hover:-translate-y-0.5 hover:shadow-md disabled:opacity-40 disabled:cursor-not-allowed"
                            :disabled="products.current_page === products.last_page"
                            @click="goToPage(products.current_page + 1)"
                        >
                            <span class="hidden sm:inline">{{ t({ ru: 'Следующая', kz: 'Келесі' }) }}</span>
                            <ChevronRight :size="20" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
