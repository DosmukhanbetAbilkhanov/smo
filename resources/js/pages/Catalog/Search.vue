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
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { PaginatedProducts, ProductFilters } from '@/types/api';
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Search as SearchIcon, X } from 'lucide-vue-next';
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
        <div class="container py-6">
            <!-- Breadcrumbs -->
            <Breadcrumb class="mb-6">
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink href="/">
                            {{ t({ ru: 'Главная', kz: 'Басты бет' }) }}
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

            <!-- Search Header -->
            <div class="mb-6">
                <h1 class="mb-4 text-3xl font-bold">
                    {{ t({ ru: 'Поиск товаров', kz: 'Тауарларды іздеу' }) }}
                </h1>

                <!-- Search Input -->
                <div class="relative max-w-2xl">
                    <SearchIcon
                        :size="20"
                        class="absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        :placeholder="t({ ru: 'Поиск товаров...', kz: 'Тауарларды іздеу...' })"
                        class="h-12 pl-10 pr-10"
                    />
                    <Button
                        v-if="searchQuery"
                        @click="clearSearch"
                        variant="ghost"
                        size="sm"
                        class="absolute top-1/2 right-2 -translate-y-1/2"
                    >
                        <X :size="16" />
                    </Button>
                </div>

                <p v-if="query" class="mt-3 text-muted-foreground">
                    {{ t({
                        ru: `Результаты поиска для "${query}": ${products.total} товаров`,
                        kz: `"${query}" іздеу нәтижелері: ${products.total} тауар`,
                    }) }}
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-4">
                <!-- Filter Sidebar -->
                <aside class="lg:col-span-1">
                    <FilterSidebar
                        :filters="filters"
                        @update:filters="updateFilters"
                        @clear="clearFilters"
                    />
                </aside>

                <!-- Results Grid -->
                <main class="lg:col-span-3">
                    <!-- Sorting and Results Count -->
                    <div v-if="query && products.total > 0" class="mb-6 flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">
                            {{ t({
                                ru: `Показано ${products.from || 0}-${products.to || 0} из ${products.total}`,
                                kz: `Көрсетілген ${products.from || 0}-${products.to || 0} ${products.total} ішінде`,
                            }) }}
                        </span>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" size="sm" class="gap-2">
                                    {{ t({
                                        ru: 'Сортировка',
                                        kz: 'Сұрыптау',
                                    }) }}:
                                    {{ t(sortOptions.find(o => o.value === currentSort)?.label || sortOptions[0].label) }}
                                </Button>
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
                    <div v-if="isLoading" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="i in 9" :key="i" class="space-y-3">
                            <Skeleton class="aspect-square w-full" />
                            <Skeleton class="h-4 w-3/4" />
                            <Skeleton class="h-4 w-1/2" />
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div
                        v-else-if="query && products.data.length > 0"
                        class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Empty State - No Query -->
                    <div
                        v-else-if="!query"
                        class="flex min-h-[400px] flex-col items-center justify-center text-center"
                    >
                        <div class="mb-4 text-6xl">🔍</div>
                        <h3 class="mb-2 text-xl font-semibold">
                            {{ t({ ru: 'Начните поиск', kz: 'Іздеуді бастаңыз' }) }}
                        </h3>
                        <p class="text-muted-foreground">
                            {{ t({
                                ru: 'Введите запрос в поисковую строку выше',
                                kz: 'Жоғарыдағы іздеу жолына сұрау енгізіңіз',
                            }) }}
                        </p>
                    </div>

                    <!-- Empty State - No Results -->
                    <div
                        v-else
                        class="flex min-h-[400px] flex-col items-center justify-center text-center"
                    >
                        <div class="mb-4 text-6xl">😕</div>
                        <h3 class="mb-2 text-xl font-semibold">
                            {{ t({ ru: 'Ничего не найдено', kz: 'Ештеңе табылмады' }) }}
                        </h3>
                        <p class="mb-4 text-muted-foreground">
                            {{ t({
                                ru: 'Попробуйте изменить запрос или фильтры',
                                kz: 'Сұрауды немесе сүзгілерді өзгертіп көріңіз',
                            }) }}
                        </p>
                        <div class="flex gap-2">
                            <Button @click="clearSearch" variant="outline">
                                {{ t({ ru: 'Очистить поиск', kz: 'Іздеуді тазалау' }) }}
                            </Button>
                            <Button @click="clearFilters" variant="outline">
                                {{ t({ ru: 'Сбросить фильтры', kz: 'Сүзгілерді тазалау' }) }}
                            </Button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="query && products.data.length > 0 && totalPages > 1"
                        class="mt-8 flex items-center justify-center gap-2"
                    >
                        <Button
                            @click="goToPage(currentPage - 1)"
                            :disabled="!hasPrevPage || isLoading"
                            variant="outline"
                            size="sm"
                        >
                            <ChevronLeft :size="16" />
                        </Button>

                        <div class="flex gap-1">
                            <Button
                                v-for="page in totalPages"
                                :key="page"
                                @click="goToPage(page)"
                                :variant="page === currentPage ? 'default' : 'outline'"
                                :disabled="isLoading"
                                size="sm"
                                class="min-w-[40px]"
                            >
                                {{ page }}
                            </Button>
                        </div>

                        <Button
                            @click="goToPage(currentPage + 1)"
                            :disabled="!hasNextPage || isLoading"
                            variant="outline"
                            size="sm"
                        >
                            <ChevronRight :size="16" />
                        </Button>
                    </div>
                </main>
            </div>
        </div>
    </ShopLayout>
</template>
