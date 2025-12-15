<script setup lang="ts">
import CitySelector from '@/components/CitySelector.vue';
import { Button } from '@/components/ui/button';
import { useCartStore } from '@/stores/cart';
import { Link, usePage } from '@inertiajs/vue3';
import { Package, ShoppingCart, User } from 'lucide-vue-next';
import { computed, onMounted, watch } from 'vue';
import LocaleSwitcher from '../shop/LocaleSwitcher.vue';
import CategoryMenu from './CategoryMenu.vue';
import SearchBar from './SearchBar.vue';

const cartStore = useCartStore();
const page = usePage();

const isAuthenticated = computed(() => !!page.props.auth?.user);
const cartItemsCount = computed(() => cartStore.itemsCount);

// Initialize cart when user is authenticated
onMounted(() => {
    if (isAuthenticated.value) {
        cartStore.initialize();
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
    <nav class="main-nav">
        <!-- Decorative Top Border -->
        <div class="nav-accent-border" />

        <div class="container mx-auto px-4">
            <div class="nav-content">
                <!-- Logo -->
                <Link href="/" class="logo-container">
                    <div class="logo-icon">
                        <Package :size="24" :stroke-width="2" />
                    </div>
                    <span class="logo-text">SMO</span>
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
                <div class="nav-actions">
                    <CitySelector />
                    <LocaleSwitcher />

                    <!-- Cart Button -->
                    <Link
                        v-if="isAuthenticated"
                        href="/cart"
                        class="cart-button"
                    >
                        <ShoppingCart :size="20" :stroke-width="2" />
                        <span
                            v-if="cartItemsCount > 0"
                            class="cart-badge"
                        >
                            {{ cartItemsCount }}
                        </span>
                    </Link>

                    <!-- Auth Buttons -->
                    <div v-if="isAuthenticated" class="auth-section">
                        <Link href="/dashboard" class="profile-button">
                            <User :size="18" :stroke-width="2" />
                            <!-- <span class="hidden sm:inline">Profile</span> -->
                        </Link>
                    </div>
                    <div v-else class="auth-section">
                        <Link href="/login" class="login-button">
                            Login
                        </Link>
                        <Link href="/register" class="register-button">
                            Register
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="mobile-search">
                <SearchBar />
            </div>
        </div>
    </nav>
</template>

<style scoped>
/* ========================================
   MAIN NAVIGATION - WARM ARCHITECTURAL THEME
   ======================================== */

.main-nav {
    position: relative;
    background: var(--smo-surface);
    border-bottom: 1px solid var(--smo-border);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

/* Decorative accent border - architectural detail */
.nav-accent-border {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(
        90deg,
        #FFB84D 0%,
        #FF9B70 25%,
        #D4A574 50%,
        #FF9B8A 75%,
        #FFAA7F 100%
    );
    opacity: 0.6;
}

.nav-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    height: 70px;
}

/* ========================================
   LOGO STYLING
   ======================================== */

.logo-container {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
    text-decoration: none;
    padding: 0.5rem 0.75rem;
    border-radius: 12px;
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.logo-container:hover {
    background: rgba(44, 95, 93, 0.04);
    transform: translateY(-1px);
}

.logo-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, rgba(44, 95, 93, 0.1) 0%, rgba(44, 95, 93, 0.05) 100%);
    border-radius: 10px;
    color: var(--smo-primary);
    box-shadow:
        0 2px 8px rgba(44, 95, 93, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.logo-container:hover .logo-icon {
    background: linear-gradient(135deg, rgba(44, 95, 93, 0.15) 0%, rgba(44, 95, 93, 0.08) 100%);
    transform: rotate(-5deg);
    box-shadow:
        0 4px 12px rgba(44, 95, 93, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.logo-text {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--smo-text-primary);
    transition: color 200ms ease;
}

.logo-container:hover .logo-text {
    color: var(--smo-primary);
}

.logo-badge {
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    font-family: var(--font-body);
    font-size: 0.625rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--smo-text-muted);
    white-space: nowrap;
    opacity: 0.7;
}

/* ========================================
   NAVIGATION ACTIONS
   ======================================== */

.nav-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.auth-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ========================================
   CART BUTTON
   ======================================== */

.cart-button {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.6);
    border: 1.5px solid var(--smo-border);
    border-radius: 10px;
    color: var(--smo-text-secondary);
    text-decoration: none;
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.cart-button:hover {
    background: rgba(255, 255, 255, 0.9);
    border-color: var(--smo-accent);
    color: var(--smo-accent);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(217, 119, 87, 0.15);
}

.cart-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--smo-accent);
    color: white;
    font-family: var(--font-display);
    font-size: 0.6875rem;
    font-weight: 700;
    border-radius: 10px;
    box-shadow:
        0 2px 8px rgba(217, 119, 87, 0.3),
        0 0 0 2px var(--smo-surface);
    animation: badge-pop 300ms cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes badge-pop {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.15);
    }
    100% {
        transform: scale(1);
    }
}

/* ========================================
   PROFILE BUTTON
   ======================================== */

.profile-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: rgba(255, 255, 255, 0.6);
    border: 1.5px solid var(--smo-border);
    border-radius: 10px;
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-text-secondary);
    text-decoration: none;
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.profile-button:hover {
    background: rgba(255, 255, 255, 0.9);
    border-color: var(--smo-primary);
    color: var(--smo-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44, 95, 93, 0.15);
}

/* ========================================
   AUTH BUTTONS
   ======================================== */

.login-button {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    background: rgba(255, 255, 255, 0.6);
    border: 1.5px solid var(--smo-border);
    border-radius: 10px;
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-text-secondary);
    text-decoration: none;
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.login-button:hover {
    background: rgba(255, 255, 255, 0.9);
    border-color: var(--smo-primary);
    color: var(--smo-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44, 95, 93, 0.15);
}

.register-button {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, var(--smo-accent) 0%, var(--smo-accent-light) 100%);
    border: none;
    border-radius: 10px;
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(217, 119, 87, 0.25);
}

.register-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(217, 119, 87, 0.35);
}

/* ========================================
   MOBILE SEARCH
   ======================================== */

.mobile-search {
    padding-bottom: 1rem;
}

@media (min-width: 768px) {
    .mobile-search {
        display: none;
    }
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

@media (max-width: 768px) {
    .nav-content {
        height: 60px;
    }

    .logo-icon {
        width: 38px;
        height: 38px;
    }

    .logo-text {
        font-size: 1.25rem;
    }

    .logo-badge {
        display: none;
    }

    .cart-button,
    .profile-button {
        width: 40px;
        height: 40px;
        padding: 0.5rem;
    }

    .profile-button span {
        display: none;
    }

    .login-button,
    .register-button {
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
    }
}

@media (max-width: 640px) {
    .nav-actions {
        gap: 0.375rem;
    }

    .cart-button {
        width: 38px;
        height: 38px;
    }

    .login-button,
    .register-button {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
    }
}

/* ========================================
   ANIMATION REFINEMENTS
   ======================================== */

@media (prefers-reduced-motion: reduce) {
    .logo-container,
    .logo-icon,
    .cart-button,
    .profile-button,
    .login-button,
    .register-button {
        transition: none;
    }

    .cart-badge {
        animation: none;
    }
}
</style>
