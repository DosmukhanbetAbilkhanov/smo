<script setup lang="ts">
import CitySelector from '@/components/CitySelector.vue';
import { useCartStore } from '@/stores/cart';
import { Link, usePage } from '@inertiajs/vue3';
import { Package, ShoppingCart, User } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import LocaleSwitcher from '../shop/LocaleSwitcher.vue';
import CategoryMenu from './CategoryMenu.vue';
import SearchBar from './SearchBar.vue';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();
const cartStore = useCartStore();
const page = usePage();

const isAuthenticated = computed(() => !!page.props.auth?.user);
const cartItemsCount = computed(() => cartStore.itemsCount);
const isSticky = ref(false);
const sentinelRef = ref<HTMLElement | null>(null);

// Initialize cart when user is authenticated
onMounted(() => {
    if (isAuthenticated.value) {
        cartStore.initialize();
    }

    // Use Intersection Observer on sentinel element to detect when nav should be sticky
    if (sentinelRef.value) {
        const observer = new IntersectionObserver(
            ([entry]) => {
                // When sentinel is NOT intersecting (out of view), make nav sticky
                isSticky.value = !entry.isIntersecting;
            },
            {
                threshold: 0,
            }
        );

        observer.observe(sentinelRef.value);

        // Cleanup
        onUnmounted(() => {
            observer.disconnect();
        });
    }
});

// Watch for authentication changes
watch(isAuthenticated, (newValue) => {
    if (newValue) {
        cartStore.initialize();
    } else {
        cartStore.reset();
    }
});
</script>

<template>
    <div>
        <!-- Sentinel element for intersection observer -->
        <div ref="sentinelRef" class="h-0" />

        <nav
            class="bg-white border-b border-concrete-200 transition-all duration-300"
            :class="{
                'fixed top-0 left-0 right-0 z-50 shadow-sm': isSticky,
                'relative': !isSticky
            }"
        >
        <!-- Decorative Top Border -->
        <!-- <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-rust-500 to-amber-600" /> -->

        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between gap-4 h-[70px] sm:h-[75px]">
                <!-- Logo -->
                <Link
                    href="/"
                    class="group flex items-center gap-3 flex-shrink-0 no-underline p-2 rounded-xl transition-all duration-200 hover:bg-steel-50 hover:-translate-y-0.5"
                >
                    <div class="flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-br from-steel-100 to-steel-50 rounded-lg text-steel-700 shadow-industrial-sm transition-all duration-200 group-hover:shadow-industrial-md group-hover:rotate-[-5deg]">
                        <Package :size="24" :stroke-width="2.5" />
                    </div>
                    <span class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-steel-900 transition-colors duration-200 group-hover:text-amber-600">
                        SMO
                    </span>
                </Link>

                <!-- Catalog Menu -->
                <div class="hidden md:block">
                    <CategoryMenu />
                </div>

                <!-- Search Bar (Full Width) -->
                <div class="hidden flex-1 md:block">
                    <SearchBar />
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <CitySelector />
                    <LocaleSwitcher />

                    <!-- Cart Button -->
                    <Link
                        v-if="isAuthenticated"
                        href="/cart"
                        class="relative flex items-center justify-center w-11 h-11 bg-white border-2 border-concrete-200 rounded-lg text-steel-600 no-underline transition-all duration-200 hover:border-amber-500 hover:text-amber-600 hover:-translate-y-0.5 hover:shadow-industrial-md"
                    >
                        <ShoppingCart :size="20" :stroke-width="2" />
                        <span
                            v-if="cartItemsCount > 0"
                            class="absolute -top-2 -right-2 flex items-center justify-center min-w-[22px] h-[22px] px-1.5 bg-amber-500 text-white font-display text-xs font-bold rounded-full shadow-industrial-sm animate-bounce-subtle"
                        >
                            {{ cartItemsCount }}
                        </span>
                    </Link>

                    <!-- Auth Buttons -->
                    <div v-if="isAuthenticated" class="flex items-center gap-2">
                        <Link
                            href="/dashboard"
                            class="flex items-center justify-center w-11 h-11 bg-white border-2 border-concrete-200 rounded-lg text-steel-600 no-underline transition-all duration-200 hover:border-steel-700 hover:text-steel-700 hover:-translate-y-0.5 hover:shadow-industrial-md"
                        >
                            <User :size="18" :stroke-width="2" />
                        </Link>
                    </div>
                    <div v-else class="flex items-center gap-2">
                        <Link
                            href="/login"
                            class="hidden sm:inline-flex items-center px-4 py-2.5 bg-white border-2 border-concrete-300 rounded-lg font-display text-sm font-semibold text-steel-700 no-underline transition-all duration-200 hover:border-steel-700 hover:bg-concrete-50 hover:-translate-y-0.5"
                        >
                            {{ t({ ru: 'Войти', kz: 'Кіру' }) }}
                        </Link>
                        <Link
                            href="/register"
                            class="inline-flex items-center px-4 py-2.5 bg-amber-500 rounded-lg font-display text-sm font-bold text-white no-underline transition-all duration-200 hover:bg-amber-600 hover:shadow-industrial-md hover:-translate-y-0.5"
                        >
                            {{ t({ ru: 'Регистрация', kz: 'Тіркелу' }) }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="pb-4 md:hidden">
                <SearchBar />
            </div>
        </div>
    </nav>
    </div>
</template>
