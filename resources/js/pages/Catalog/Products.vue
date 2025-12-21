<script setup lang="ts">
import ProductCard from '@/components/shop/ProductCard.vue';
import PageBreadcrumb from '@/components/PageBreadcrumb.vue';
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
    { value: 'newest', label: { ru: 'Новинки', kz: 'Жаңалықтар' } },
    { value: 'price_asc', label: { ru: 'Цена: по возрастанию', kz: 'Бағасы: өсу' } },
    { value: 'price_desc', label: { ru: 'Цена: по убыванию', kz: 'Бағасы: кему' } },
    { value: 'name', label: { ru: 'По названию', kz: 'Атауы бойынша' } },
] as const;

const currentSort = ref<ProductFilters['sort']>(props.filters.sort || 'newest');
const isLoading = ref(false);

const currentPage = computed(() => props.products.current_page);
const totalPages = computed(() => props.products.last_page);
const hasNextPage = computed(() => currentPage.value < totalPages.value);
const hasPrevPage = computed(() => currentPage.value > 1);

const breadcrumbItems = computed(() => [
    { label: t({ ru: 'Главная', kz: 'Басты бет' }), href: '/' },
    { label: t({ ru: 'Каталог товаров', kz: 'Тауарлар каталогы' }), isCurrentPage: true },
]);

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
        sort: 'newest',
    });
}

function updateSort(value: string) {
    const sortValue = value as ProductFilters['sort'];
    currentSort.value = sortValue;
    updateFilters({ sort: sortValue });
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
        <!-- Breadcrumbs -->
        <PageBreadcrumb :items="breadcrumbItems" variant="minimal" />

        <div class="-mx-4 min-h-screen pb-16 bg-[var(--smo-bg)] font-[var(--font-body)]">
            <div class="px-4">

                <!-- Page Header -->
                <div class="animate-fadeInUp">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                        <h1 class="page-title">
                            {{ t({ ru: 'Каталог товаров', kz: 'Тауарлар каталогы' }) }}
                        </h1>
                        <div class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-[var(--smo-primary)] bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] border border-[rgba(44,95,93,0.2)] rounded-[var(--radius-lg)] font-[var(--font-display)]">
                            <Package :size="16" />
                            <span>{{ products.total }} {{ t({ ru: 'товаров', kz: 'тауар' }) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Products Main -->
                <main class="flex flex-col gap-8">
                    <!-- Sorting and Results Count -->
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-between gap-4 px-6 py-5 bg-[var(--smo-surface)] border border-[var(--smo-border)] rounded-[var(--radius-lg)] shadow-sm animate-fadeInUp" style="animation-delay: 200ms">
                        <span class="text-sm font-medium text-[var(--smo-text-secondary)] font-[var(--font-body)]">
                            {{ t({
                                ru: `Показано ${products.from || 0}-${products.to || 0} из ${products.total}`,
                                kz: `Көрсетілген ${products.from || 0}-${products.to || 0} ${products.total} ішінде`,
                            }) }}
                        </span>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <button class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2.5 text-sm font-semibold text-[var(--smo-text-primary)] bg-[var(--smo-bg)] border-2 border-[var(--smo-border)] rounded-[var(--radius-md)] font-[var(--font-display)] transition-all hover:border-[var(--smo-primary)] hover:bg-[var(--smo-surface)] cursor-pointer">
                                    <SlidersHorizontal :size="16" />
                                    {{ t({
                                        ru: 'Сортировка',
                                        kz: 'Сұрыптау',
                                    }) }}:
                                    {{ t(sortOptions.find(o => o.value === currentSort)?.label || sortOptions[0].label) }}
                                </button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="min-w-[200px]">
                                <DropdownMenuItem
                                    v-for="option in sortOptions"
                                    :key="option.value"
                                    @click="updateSort(option.value)"
                                    :class="{ 'bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] text-[var(--smo-primary)] font-semibold': currentSort === option.value }"
                                >
                                    {{ t(option.label) }}
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>

                    <!-- Loading State -->
                    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="i in 8" :key="i" class="flex flex-col gap-3">
                            <Skeleton class="aspect-square w-full rounded-[var(--radius-md)]" />
                            <Skeleton class="h-4 w-3/4 rounded-[var(--radius-sm)]" />
                            <Skeleton class="h-4 w-1/2 rounded-[var(--radius-sm)]" />
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div
                        v-else-if="products.data.length > 0"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
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
                    <div v-else class="flex flex-col items-center justify-center min-h-[400px] text-center px-4 py-12">
                        <div class="flex items-center justify-center w-32 h-32 mb-6 text-[var(--smo-primary)] bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] rounded-full">
                            <Package :size="64" />
                        </div>
                        <h3 class="mb-2 text-2xl font-bold text-[var(--smo-text-primary)] font-[var(--font-display)]">
                            {{ t({ ru: 'Товары не найдены', kz: 'Тауарлар табылмады' }) }}
                        </h3>
                        <p class="mb-6 max-w-[500px] text-base text-[var(--smo-text-secondary)] font-[var(--font-body)]">
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
                        class="flex items-center justify-center gap-2 mt-8"
                    >
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="!hasPrevPage || isLoading"
                            class="flex items-center justify-center w-10 h-10 text-[var(--smo-text-primary)] bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-md)] transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-primary)] hover:bg-[var(--smo-bg)] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        >
                            <ChevronLeft :size="16" />
                        </button>

                        <div class="flex gap-1">
                            <button
                                v-for="page in totalPages"
                                :key="page"
                                @click="goToPage(page)"
                                :class="[
                                    'flex items-center justify-center min-w-[40px] h-10 px-3 text-sm font-semibold text-[var(--smo-text-primary)] bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-md)] font-[var(--font-display)] transition-all cursor-pointer',
                                    page === currentPage
                                        ? 'bg-gradient-to-br from-[var(--smo-primary)] to-[var(--smo-primary-light)] border-[var(--smo-primary)]  shadow-[0_4px_12px_rgba(44,95,93,0.3)]'
                                        : 'hover:border-[var(--smo-primary-light)] hover:text-[var(--smo-primary)]',
                                    'disabled:opacity-50 disabled:cursor-not-allowed'
                                ]"
                                :disabled="isLoading"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="!hasNextPage || isLoading"
                            class="flex items-center justify-center w-10 h-10 text-[var(--smo-text-primary)] bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-md)] transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-primary)] hover:bg-[var(--smo-bg)] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        >
                            <ChevronRight :size="16" />
                        </button>
                    </div>
                </main>
            </div>
        </div>
    </ShopLayout>
</template>

