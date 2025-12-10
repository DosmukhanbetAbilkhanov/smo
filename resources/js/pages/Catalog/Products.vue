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
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
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
                            {{ t({ ru: 'Каталог товаров', kz: 'Тауарлар каталогы' }) }}
                        </BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold">
                    {{ t({ ru: 'Каталог товаров', kz: 'Тауарлар каталогы' }) }}
                </h1>
                <p class="mt-2 text-muted-foreground">
                    {{ t({
                        ru: `Найдено товаров: ${products.total}`,
                        kz: `Табылған тауарлар: ${products.total}`,
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

                <!-- Products Grid -->
                <main class="lg:col-span-3">
                    <!-- Sorting and Results Count -->
                    <div class="mb-6 flex items-center justify-between">
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
                        v-else-if="products.data.length > 0"
                        class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
                        class="flex min-h-[400px] flex-col items-center justify-center text-center"
                    >
                        <div class="mb-4 text-6xl">📦</div>
                        <h3 class="mb-2 text-xl font-semibold">
                            {{ t({ ru: 'Товары не найдены', kz: 'Тауарлар табылмады' }) }}
                        </h3>
                        <p class="mb-4 text-muted-foreground">
                            {{ t({
                                ru: 'Попробуйте изменить фильтры или параметры поиска',
                                kz: 'Сүзгілерді немесе іздеу параметрлерін өзгертіп көріңіз',
                            }) }}
                        </p>
                        <Button @click="clearFilters" variant="outline">
                            {{ t({ ru: 'Сбросить фильтры', kz: 'Сүзгілерді тазалау' }) }}
                        </Button>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="products.data.length > 0 && totalPages > 1"
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
