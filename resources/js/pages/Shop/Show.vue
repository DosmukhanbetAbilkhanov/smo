<script setup lang="ts">
import ProductCard from '@/components/shop/ProductCard.vue';
import PageBreadcrumb from '@/components/PageBreadcrumb.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category, PaginatedProducts, Shop } from '@/types/api';
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Grid, MapPin, Package, Search, X } from 'lucide-vue-next';
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

const breadcrumbItems = computed(() => [
    { label: t({ ru: 'Главная', kz: 'Басты бет' }), href: '/' },
    { label: props.shop.name, isCurrentPage: true },
]);

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
        <!-- Breadcrumbs -->
        <PageBreadcrumb :items="breadcrumbItems" variant="minimal" />

        <div class="-mx-4 shop-page bg-pattern">
            <div class="px-4">

                <!-- Shop Header -->
                <div class="flex gap-3  py-2 bg-white rounded-lg mb-2">
                        <h1 class="font-bold text-gray-900">{{ shop.name }}</h1>
                        <div class="flex flex-wrap gap-4">
                            <div v-if="shop.city" class="flex items-center gap-2 text-sm text-slate-400">
                                <MapPin :size="12"/>
                                <span>{{ shop.city.name_ru }}</span>
                            </div>
                         
                        </div>
                </div>

                <!-- Search Section -->
                <div class="mb-6">
                    <div class="relative">
                        <Search :size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" />
                        <Input
                            v-model="localSearchQuery"
                            type="text"
                            :placeholder="t({ ru: 'Поиск товаров...', kz: 'Тауарларды іздеу...' })"
                            class="w-full pl-10 pr-10 py-2.5 text-sm placeholder:text-gray-400"
                        />
                        <button
                            v-if="localSearchQuery"
                            @click="clearSearch"
                            class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center justify-center w-7 h-7 rounded border-0 bg-gray-100 text-gray-400 cursor-pointer transition-all hover:bg-gray-200 hover:text-gray-900"
                        >
                            <X :size="16" />
                        </button>
                    </div>
                </div>

                <!-- Categories Navigation -->
                <div
                    v-if="categories.length > 0"
                    class="bg-white rounded-md border border-gray-200 p-5 mb-6 animate-fadeInUp"
                    style="animation-delay: 200ms"
                >
                    <div class="flex flex-wrap gap-6">
                        <div class="flex flex-col gap-1">
                            <a
                                @click.prevent="filterByCategory(null)"
                                :class="['text-sm cursor-pointer transition-opacity hover:opacity-70 no-underline block', !selectedCategoryId ? 'text-black font-bold underline' : 'text-black font-bold']"
                                href="#"
                            >
                                {{ t({ ru: 'Все товары', kz: 'Барлық тауарлар' }) }}
                            </a>
                        </div>
                        <div v-for="category in categories" :key="category.id" class="flex flex-col gap-1">
                            <span class="text-black font-bold mb-1 text-sm">
                                {{ getLocalizedName(category) }}
                            </span>
                            <div v-if="category.children && category.children.length > 0" class="flex flex-col gap-1">
                                <a
                                    v-for="child in category.children"
                                    :key="child.id"
                                    @click.prevent="filterByCategory(child.id)"
                                    :class="['text-sm cursor-pointer transition-opacity hover:opacity-70 block', selectedCategoryId === child.id ? 'text-blue-600 font-bold underline' : 'text-blue-600 font-normal underline']"
                                    href="#"
                                >
                                    {{ getLocalizedName(child) }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="flex flex-col gap-8">
                    <div class="flex items-center justify-between flex-wrap gap-4 animate-fadeInUp" style="animation-delay: 300ms">
                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ t({ ru: 'Товары магазина', kz: 'Дүкен тауарлары' }) }}
                        </h2>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] border border-[rgba(44,95,93,0.2)] rounded-lg text-[15px] font-semibold text-[#2C5F5D]">
                            <Package :size="16" />
                            <span>{{ products.total }} {{ t({ ru: 'товаров', kz: 'тауар' }) }}</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div v-for="i in 9" :key="i" class="flex flex-col gap-3">
                            <Skeleton class="aspect-square w-full rounded-md" />
                            <Skeleton class="h-4 w-3/4 rounded-sm" />
                            <Skeleton class="h-4 w-1/2 rounded-sm" />
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div
                        v-else-if="products.data.length > 0"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fadeInUp"
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
                    <div v-else class="flex flex-col items-center justify-center min-h-[400px] text-center px-4 py-12">
                        <div class="flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] text-[#2C5F5D] mb-6">
                            <Package :size="64" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ t({ ru: 'В магазине пока нет товаров', kz: 'Дүкенде әлі тауарлар жоқ' }) }}
                        </h3>
                        <p class="text-base text-gray-600 max-w-lg">
                            {{ t({
                                ru: 'Проверьте позже или посмотрите другие магазины',
                                kz: 'Кейінірек тексеріңіз немесе басқа дүкендерді қараңыз',
                            }) }}
                        </p>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="products.data.length > 0 && totalPages > 1"
                        class="flex items-center justify-center gap-2 mt-8"
                    >
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="!hasPrevPage || isLoading"
                            class="flex items-center justify-center w-10 h-10 bg-white border-2 border-gray-200 rounded-md text-gray-900 cursor-pointer transition-all hover:border-[#2C5F5D] hover:text-[#2C5F5D] hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ChevronLeft :size="16" />
                        </button>

                        <div class="flex gap-1">
                            <button
                                v-for="page in totalPages"
                                :key="page"
                                @click="goToPage(page)"
                                :class="[
                                    'flex items-center justify-center min-w-[40px] h-10 px-3 border-2 rounded-md text-[15px] font-semibold cursor-pointer transition-all',
                                    page === currentPage
                                        ? 'bg-gradient-to-br from-[#2C5F5D] to-[#3A7D7A] border-[#2C5F5D] text-white shadow-[0_4px_12px_rgba(44,95,93,0.3)]'
                                        : 'bg-white border-gray-200 text-gray-900 hover:border-[#3A7D7A] hover:text-[#2C5F5D]',
                                    isLoading && 'opacity-50 cursor-not-allowed'
                                ]"
                                :disabled="isLoading"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="!hasNextPage || isLoading"
                            class="flex items-center justify-center w-10 h-10 bg-white border-2 border-gray-200 rounded-md text-gray-900 cursor-pointer transition-all hover:border-[#2C5F5D] hover:text-[#2C5F5D] hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
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
/* Shop Page Background */
.shop-page {
    background: var(--smo-bg);
    min-height: 100vh;
    padding-bottom: 4rem;
}

/* Animation */
.animate-fadeInUp {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
