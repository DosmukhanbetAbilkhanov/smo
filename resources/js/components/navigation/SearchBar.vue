<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { useLocale } from '@/composables/useLocale';
import type { Product } from '@/types/api';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { Loader2, Search, Tag, X } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const { t } = useLocale();

const searchQuery = ref('');
const productSuggestions = ref<Product[]>([]);
const categorySuggestions = ref<any[]>([]);
const isLoading = ref(false);
const showDropdown = ref(false);
const searchDebounce = ref<number | null>(null);
const searchContainer = ref<HTMLElement | null>(null);

const hasSuggestions = computed(
    () => productSuggestions.value.length > 0 || categorySuggestions.value.length > 0
);

async function fetchSuggestions(query: string) {
    if (!query.trim() || query.length < 2) {
        productSuggestions.value = [];
        categorySuggestions.value = [];
        showDropdown.value = false;
        return;
    }

    isLoading.value = true;

    try {
        // Fetch both products and categories in parallel
        const [productsResponse, categoriesResponse] = await Promise.all([
            axios.get('/api/v1/products/search', {
                params: { q: query, limit: 6 },
            }),
            axios.get('/api/v1/categories', {
                params: { search: query, limit: 4 },
            }),
        ]);

        productSuggestions.value = productsResponse.data.data?.data || [];
        categorySuggestions.value = categoriesResponse.data.data || [];

        showDropdown.value = hasSuggestions.value;

        console.log(`Search for "${query}":`, {
            products: productSuggestions.value.length,
            categories: categorySuggestions.value.length
        });
    } catch (error) {
        console.error('Failed to fetch suggestions:', error);
        productSuggestions.value = [];
        categorySuggestions.value = [];
    } finally {
        isLoading.value = false;
    }
}

function handleInput() {
    if (searchDebounce.value) {
        clearTimeout(searchDebounce.value);
    }

    searchDebounce.value = window.setTimeout(() => {
        fetchSuggestions(searchQuery.value);
    }, 300);
}

function handleSearch() {
    if (searchQuery.value.trim()) {
        showDropdown.value = false;
        router.get('/search', { q: searchQuery.value });
    }
}

function selectProduct(product: Product) {
    showDropdown.value = false;
    router.get(`/products/${product.id}`);
}

function selectCategory(category: any) {
    showDropdown.value = false;
    router.get(`/categories/${category.slug}`);
}

function clearSearch() {
    searchQuery.value = '';
    productSuggestions.value = [];
    categorySuggestions.value = [];
    showDropdown.value = false;
}

function handleClickOutside(event: MouseEvent) {
    if (
        searchContainer.value &&
        !searchContainer.value.contains(event.target as Node)
    ) {
        showDropdown.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (searchDebounce.value) {
        clearTimeout(searchDebounce.value);
    }
});
</script>

<template>
    <div ref="searchContainer" class="relative w-full">
        <!-- Search Input -->
        <div class="relative">
            <Search
                :size="18"
                class="absolute top-1/2 left-3 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="searchQuery"
                type="search"
                :placeholder="t({ ru: 'Поиск товаров...', kz: 'Тауарларды іздеу...' })"
                class="h-10 pl-10 pr-10"
                @input="handleInput"
                @keyup.enter="handleSearch"
                @focus="searchQuery && (showDropdown = true)"
            />
            <div
                class="absolute top-1/2 right-2 -translate-y-1/2 flex items-center gap-1"
            >
                <Loader2
                    v-if="isLoading"
                    :size="16"
                    class="animate-spin text-muted-foreground"
                />
                <button
                    v-if="searchQuery && !isLoading"
                    @click="clearSearch"
                    class="rounded-sm p-1 hover:bg-accent"
                    type="button"
                >
                    <X :size="14" />
                </button>
            </div>
        </div>

        <!-- Suggestions Dropdown -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="showDropdown && hasSuggestions"
                class="absolute top-full left-0 right-0 z-50 mt-2 max-h-[500px] overflow-y-auto rounded-lg border bg-popover shadow-lg"
            >
                <div class="p-2">
                    <!-- Categories Section -->
                    <div v-if="categorySuggestions.length > 0" class="mb-3">
                        <div class="mb-2 px-2 text-xs font-medium text-muted-foreground">
                            {{ t({ ru: 'Категории', kz: 'Санаттар' }) }}
                        </div>
                        <button
                            v-for="category in categorySuggestions"
                            :key="category.id"
                            @click="selectCategory(category)"
                            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left transition-colors hover:bg-accent"
                        >
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary/10"
                            >
                                <Tag :size="18" class="text-primary" />
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="truncate text-sm font-medium">
                                    {{ category.name_ru }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ t({ ru: 'Категория', kz: 'Санат' }) }}
                                </p>
                            </div>
                        </button>
                    </div>

                    <!-- Products Section -->
                    <div v-if="productSuggestions.length > 0">
                        <div class="mb-2 px-2 text-xs font-medium text-muted-foreground">
                            {{ t({ ru: 'Товары', kz: 'Тауарлар' }) }}
                        </div>
                        <button
                            v-for="product in productSuggestions"
                            :key="product.id"
                            @click="selectProduct(product)"
                            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left transition-colors hover:bg-accent"
                        >
                            <div
                                class="h-12 w-12 flex-shrink-0 overflow-hidden rounded border bg-muted"
                            >
                                <img
                                    v-if="product.images?.[0]"
                                    :src="product.images[0]"
                                    :alt="product.name_ru"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="truncate text-sm font-medium">
                                    {{ product.name_ru }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ product.price }} ₸
                                    <span v-if="product.shop" class="ml-2">
                                        · {{ product.shop.name }}
                                    </span>
                                </p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- View All Results -->
                <div class="border-t p-2">
                    <button
                        @click="handleSearch"
                        class="flex w-full items-center justify-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-accent"
                    >
                        <Search :size="14" />
                        {{ t({
                            ru: `Показать все результаты для "${searchQuery}"`,
                            kz: `"${searchQuery}" үшін барлық нәтижелерді көрсету`,
                        }) }}
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
