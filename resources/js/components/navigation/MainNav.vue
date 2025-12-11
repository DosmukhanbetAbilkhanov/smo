<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useCartStore } from '@/stores/cart';
import { Link, usePage } from '@inertiajs/vue3';
import { Package, ShoppingBag, User } from 'lucide-vue-next';
import { computed } from 'vue';
import LocaleSwitcher from '../shop/LocaleSwitcher.vue';
import CategoryMenu from './CategoryMenu.vue';
import SearchBar from './SearchBar.vue';

const cartStore = useCartStore();
const page = usePage();

const isAuthenticated = computed(() => !!page.props.auth?.user);
const cartItemsCount = computed(() => cartStore.itemsCount);

// Fetch cart on mount if user is authenticated
if (isAuthenticated.value) {
    cartStore.fetchCart().catch(() => {
        // Ignore errors on initial load
    });
}
</script>

<template>
    <nav class="border-b bg-background">
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center gap-4">
                <!-- Logo -->
                <Link href="/" class="flex flex-shrink-0 items-center gap-2">
                    <Package :size="24" class="text-primary" />
                    <span class="text-xl font-bold">SMO</span>
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
                <div class="flex flex-shrink-0 items-center gap-2">
                    <LocaleSwitcher />

                    <!-- Cart Button -->
                    <Button
                        v-if="isAuthenticated"
                        variant="ghost"
                        size="sm"
                        as-child
                        class="relative"
                    >
                        <Link href="/cart">
                            <ShoppingBag :size="18" />
                            <span
                                v-if="cartItemsCount > 0"
                                class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs text-primary-foreground"
                            >
                                {{ cartItemsCount }}
                            </span>
                        </Link>
                    </Button>

                    <!-- Auth Buttons -->
                    <div v-if="isAuthenticated" class="flex items-center gap-2">
                        <Button variant="ghost" size="sm" as-child>
                            <Link href="/dashboard">
                                <User :size="16" class="mr-2" />
                                <span class="hidden sm:inline">Profile</span>
                            </Link>
                        </Button>
                    </div>
                    <div v-else class="flex items-center gap-2">
                        <Button variant="ghost" size="sm" as-child>
                            <Link href="/login">Login</Link>
                        </Button>
                        <Button size="sm" as-child>
                            <Link href="/register">Register</Link>
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="pb-3 md:hidden">
                <SearchBar />
            </div>
        </div>
    </nav>
</template>
