<script setup lang="ts">
import FilterSidebar from '@/components/catalog/FilterSidebar.vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import PageBreadcrumb from '@/components/PageBreadcrumb.vue';
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

const breadcrumbItems = computed(() => [
    { icon: Home, href: '/' },
    { label: t({ ru: 'Поиск', kz: 'Іздеу' }), isCurrentPage: true },
]);

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
        <PageBreadcrumb :items="breadcrumbItems" />

        <!-- Search Page -->
        <div class="-mx-4 min-h-screen bg-[var(--smo-bg)] bg-pattern">
            <div class="px-4">
                <!-- Search Header -->
                <div class="mb-8 animate-fadeInUp">
                    <h1 class="page-title">
                        {{ t({ ru: 'Поиск товаров', kz: 'Тауарларды іздеу' }) }}
                    </h1>

                    <!-- Search Input -->
                    <div class="relative max-w-[700px] my-6">
                        <SearchIcon :size="20" class="absolute top-1/2 left-4 -translate-y-1/2 text-[var(--smo-text-muted)] pointer-events-none" />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            :placeholder="t({ ru: 'Поиск товаров...', kz: 'Тауарларды іздеу...' })"
                            class="h-14 pl-12 pr-12 font-[var(--font-body)] text-base border-2 border-[var(--smo-border)] rounded-[var(--radius-lg)] bg-[var(--smo-surface)] transition-all hover:border-[var(--smo-primary-light)] focus:outline-none focus:border-[var(--smo-primary)] focus:shadow-[0_0_0_3px_rgba(44,95,93,0.1)]"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="absolute top-1/2 right-3 -translate-y-1/2 flex items-center justify-center w-9 h-9 bg-transparent border-none rounded-[var(--radius-sm)] text-[var(--smo-text-muted)] cursor-pointer transition-all hover:bg-[var(--smo-bg)] hover:text-[var(--smo-text-primary)]"
                        >
                            <X :size="20" />
                        </button>
                    </div>

                    <div v-if="query" class="flex items-center gap-4 flex-wrap">
                        <span class="font-[var(--font-display)] text-lg font-semibold text-[var(--smo-primary)]">"{{ query }}"</span>
                        <span class="font-[var(--font-body)] text-[0.9375rem] text-[var(--smo-text-secondary)]">
                            {{ t({
                                ru: `${products.total} товаров найдено`,
                                kz: `${products.total} тауар табылды`,
                            }) }}
                        </span>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
                    <!-- Filter Sidebar -->
                    <aside class="bg-[var(--smo-surface)] border border-[var(--smo-border)] rounded-[var(--radius-lg)] p-6 shadow-sm animate-fadeInUp" style="animation-delay: 100ms">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-[var(--smo-border)]">
                            <SlidersHorizontal :size="20" class="text-[var(--smo-primary)]" />
                            <h2 class="font-[var(--font-display)] text-lg font-bold text-[var(--smo-text-primary)]">
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
                    <main class="flex flex-col gap-6">
                        <!-- Toolbar -->
                        <div v-if="query && products.total > 0" class="flex items-center justify-between px-6 py-4 bg-[var(--smo-surface)] border border-[var(--smo-border)] rounded-[var(--radius-md)] shadow-sm animate-fadeInUp" style="animation-delay: 200ms">
                            <div class="flex items-center gap-4">
                                <span class="font-[var(--font-body)] text-[0.9375rem] font-medium text-[var(--smo-text-secondary)]">
                                    {{ t({
                                        ru: `${products.from || 0}-${products.to || 0} из ${products.total}`,
                                        kz: `${products.from || 0}-${products.to || 0} ${products.total} ішінде`,
                                    }) }}
                                </span>
                            </div>

                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <button class="flex items-center gap-2 px-4 py-2 bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-sm)] font-[var(--font-display)] text-sm font-semibold text-[var(--smo-text-secondary)] cursor-pointer transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-text-primary)]">
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
                        <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="i in 9" :key="i" class="flex flex-col gap-3">
                                <Skeleton class="aspect-square w-full rounded-[var(--radius-md)]" />
                                <Skeleton class="h-4 w-3/4 rounded-[var(--radius-sm)]" />
                                <Skeleton class="h-4 w-1/2 rounded-[var(--radius-sm)]" />
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div
                            v-else-if="query && products.data.length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
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
                        <div v-else-if="!query" class="flex flex-col items-center justify-center min-h-[400px] px-12 py-12 bg-[var(--smo-surface)] border-2 border-dashed border-[var(--smo-border)] rounded-[var(--radius-lg)] text-center animate-fadeInUp">
                            <div class="flex items-center justify-center w-[120px] h-[120px] mb-6 bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] rounded-full text-[var(--smo-primary)]">
                                <SearchIcon :size="64" />
                            </div>
                            <h3 class="font-[var(--font-display)] text-2xl font-bold text-[var(--smo-text-primary)] mb-2">
                                {{ t({ ru: 'Начните поиск', kz: 'Іздеуді бастаңыз' }) }}
                            </h3>
                            <p class="font-[var(--font-body)] text-base text-[var(--smo-text-secondary)] max-w-[400px] mb-6">
                                {{ t({
                                    ru: 'Введите запрос в поисковую строку выше',
                                    kz: 'Жоғарыдағы іздеу жолына сұрау енгізіңіз',
                                }) }}
                            </p>
                        </div>

                        <!-- Empty State - No Results -->
                        <div v-else class="flex flex-col items-center justify-center min-h-[400px] px-12 py-12 bg-[var(--smo-surface)] border-2 border-dashed border-[var(--smo-border)] rounded-[var(--radius-lg)] text-center animate-fadeInUp">
                            <div class="flex items-center justify-center w-[120px] h-[120px] mb-6 bg-gradient-to-br from-[rgba(217,119,87,0.1)] to-[rgba(217,119,87,0.05)] rounded-full text-[var(--smo-accent)]">
                                <SearchIcon :size="64" />
                            </div>
                            <h3 class="font-[var(--font-display)] text-2xl font-bold text-[var(--smo-text-primary)] mb-2">
                                {{ t({ ru: 'Ничего не найдено', kz: 'Ештеңе табылмады' }) }}
                            </h3>
                            <p class="font-[var(--font-body)] text-base text-[var(--smo-text-secondary)] max-w-[400px] mb-6">
                                {{ t({
                                    ru: 'Попробуйте изменить запрос или фильтры',
                                    kz: 'Сұрауды немесе сүзгілерді өзгертіп көріңіз',
                                }) }}
                            </p>
                            <div class="flex gap-3 flex-wrap">
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
                            class="flex items-center justify-center gap-3 mt-8 animate-fadeInUp"
                        >
                            <button
                                @click="goToPage(currentPage - 1)"
                                :disabled="!hasPrevPage || isLoading"
                                class="flex items-center justify-center w-10 h-10 bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-sm)] text-[var(--smo-text-secondary)] cursor-pointer transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-text-primary)] hover:-translate-y-0.5 hover:shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                <ChevronLeft :size="20" />
                            </button>

                            <div class="flex gap-2">
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :disabled="isLoading"
                                    :class="[
                                        'flex items-center justify-center min-w-[40px] h-10 px-3 bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-sm)] font-[var(--font-display)] text-[0.9375rem] font-semibold text-[var(--smo-text-secondary)] cursor-pointer transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-text-primary)] hover:-translate-y-0.5 hover:shadow-sm disabled:opacity-40 disabled:cursor-not-allowed',
                                        page === currentPage ? 'bg-[var(--smo-primary)] border-[var(--smo-primary)] text-white' : ''
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <button
                                @click="goToPage(currentPage + 1)"
                                :disabled="!hasNextPage || isLoading"
                                class="flex items-center justify-center w-10 h-10 bg-[var(--smo-surface)] border-2 border-[var(--smo-border)] rounded-[var(--radius-sm)] text-[var(--smo-text-secondary)] cursor-pointer transition-all hover:border-[var(--smo-primary)] hover:text-[var(--smo-text-primary)] hover:-translate-y-0.5 hover:shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
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
