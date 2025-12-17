<script setup lang="ts">
import CheckoutProgress from '@/components/checkout/CheckoutProgress.vue';
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { useCartStore } from '@/stores/cart';
import type { CartItem } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { Minus, Plus, ShoppingBag, ShoppingCart, Store, Trash2, X } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const cartStore = useCartStore();
const { getLocalizedName } = useLocale();
const removing = ref<number | null>(null);
const updating = ref<number | null>(null);

const cartsWithItems = computed(() => {
    return cartStore.carts.filter(cart => cart.items && cart.items.length > 0);
});

function getProductName(item: CartItem) {
    return item.product ? getLocalizedName(item.product) : 'Product';
}

onMounted(async () => {
    await cartStore.fetchCart();
});

async function handleUpdateQuantity(itemId: number, newQuantity: number) {
    if (newQuantity < 1) return;

    updating.value = itemId;
    try {
        await cartStore.updateQuantity(itemId, { quantity: newQuantity });
    } catch (error) {
        console.error('Failed to update quantity:', error);
    } finally {
        updating.value = null;
    }
}

async function handleRemoveItem(itemId: number) {
    removing.value = itemId;
    try {
        await cartStore.removeItem(itemId);
    } catch (error) {
        console.error('Failed to remove item:', error);
    } finally {
        removing.value = null;
    }
}

async function handleClearCart() {
    if (!confirm('Are you sure you want to clear your cart?')) return;

    try {
        await cartStore.clearCart();
    } catch (error) {
        console.error('Failed to clear cart:', error);
    }
}

function getItemSubtotal(item: any) {
    return item.price * item.quantity;
}

function canCheckout(cart: any) {
    if (!cart.shop?.min_order_amount) return true;
    return cart.total >= cart.shop.min_order_amount;
}

function getRemainingAmount(cart: any) {
    if (!cart.shop?.min_order_amount) return 0;
    return Math.max(0, cart.shop.min_order_amount - cart.total);
}
</script>

<template>
    <Head title="Shopping Cart" />

    <ShopLayout>
        <div class="cart-page bg-pattern">
            <!-- Progress Indicator -->
            <CheckoutProgress :current-step="1" />

            <div class="page-container">
                <!-- Page Header -->
                <div class="page-header animate-fadeInUp">
                    <h1 class="page-title">Shopping Cart</h1>
                    <p class="page-subtitle">
                        Review your items and proceed to checkout
                    </p>
                </div>

                <!-- Loading State -->
                <div
                    v-if="cartStore.loading && cartStore.carts.length === 0"
                    class="loading-state"
                >
                    <div class="loading-spinner"></div>
                    <p class="loading-text">Loading cart...</p>
                </div>

                <!-- Empty State -->
                <div
                    v-else-if="cartStore.isEmpty"
                    class="empty-state animate-fadeIn"
                >
                    <div class="empty-icon">
                        <ShoppingBag :size="48" />
                    </div>
                    <h2 class="empty-title">Your cart is empty</h2>
                    <p class="empty-subtitle">
                        Add some products to get started
                    </p>
                    <Button class="btn-primary-modern mt-6" as-child>
                        <Link href="/products">Browse Products</Link>
                    </Button>
                </div>

                <!-- Cart Content -->
                <div v-else class="cart-content">
                    <!-- Header with Clear All Button -->
                    <div class="cart-header animate-fadeInUp">
                        <div>
                            <h2 class="cart-title">Your Cart</h2>
                            <p class="cart-subtitle">
                                {{ cartStore.itemsCount }} {{ cartStore.itemsCount === 1 ? 'item' : 'items' }}
                                from {{ cartsWithItems.length }} {{ cartsWithItems.length === 1 ? 'shop' : 'shops' }}
                            </p>
                        </div>
                        <button
                            @click="handleClearCart"
                            :disabled="cartStore.loading"
                            class="btn-clear-all"
                        >
                            <Trash2 :size="16" />
                            Clear All
                        </button>
                    </div>

                    <!-- Cart Items Grouped by Shop -->
                    <div class="shops-list">
                        <div
                            v-for="(cart, index) in cartsWithItems"
                            :key="cart.id"
                            class="shop-card animate-fadeInUp"
                            :style="{ animationDelay: `${index * 100}ms` }"
                        >
                            <!-- Shop Header -->
                            <div class="shop-header">
                                <div class="shop-info">
                                    <Store :size="20" class="shop-icon" />
                                    <div>
                                        <h3 class="shop-name">{{ cart.shop?.name }}</h3>
                                        <p class="shop-items">
                                            {{ cart.items_count }} {{ cart.items_count === 1 ? 'item' : 'items' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Items -->
                            <div class="shop-items-list">
                                <div
                                    v-for="item in cart.items"
                                    :key="item.id"
                                    class="cart-item"
                                >
                                    <!-- Product Image -->
                                    <Link
                                        :href="`/products/${item.product_id}`"
                                        class="item-image-link"
                                    >
                                        <div class="item-image">
                                            <img
                                                v-if="item.product?.images?.[0]"
                                                :src="item.product.images[0]"
                                                :alt="getProductName(item)"
                                            />
                                            <div v-else class="item-image-placeholder">
                                                <span>📦</span>
                                            </div>
                                        </div>
                                    </Link>

                                    <!-- Product Info -->
                                    <div class="item-details">
                                        <div class="item-header">
                                            <Link :href="`/products/${item.product_id}`">
                                                <h4 class="item-name">
                                                    {{ getProductName(item) }}
                                                </h4>
                                            </Link>
                                            <button
                                                @click="handleRemoveItem(item.id)"
                                                :disabled="removing === item.id"
                                                class="btn-remove"
                                            >
                                                <X :size="16" />
                                            </button>
                                        </div>

                                        <div class="item-footer">
                                            <!-- Quantity Controls -->
                                            <div class="quantity-controls">
                                                <button
                                                    @click="handleUpdateQuantity(item.id, item.quantity - 1)"
                                                    :disabled="item.quantity <= 1 || updating === item.id"
                                                    class="qty-btn"
                                                >
                                                    <Minus :size="14" />
                                                </button>
                                                <Input
                                                    type="number"
                                                    :value="item.quantity"
                                                    @input="
                                                        (e: Event) =>
                                                            handleUpdateQuantity(
                                                                item.id,
                                                                parseInt(
                                                                    (e.target as HTMLInputElement).value,
                                                                ),
                                                            )
                                                    "
                                                    class="qty-input"
                                                    min="1"
                                                />
                                                <button
                                                    @click="handleUpdateQuantity(item.id, item.quantity + 1)"
                                                    :disabled="updating === item.id"
                                                    class="qty-btn"
                                                >
                                                    <Plus :size="14" />
                                                </button>
                                            </div>

                                            <!-- Item Pricing -->
                                            <div class="item-pricing">
                                                <PriceDisplay
                                                    :price="item.price"
                                                    class="item-unit-price"
                                                />
                                                <PriceDisplay
                                                    :price="getItemSubtotal(item)"
                                                    class="item-subtotal"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Footer -->
                            <div class="shop-footer">
                                <div class="shop-total-section">
                                    <div class="shop-total">
                                        <span class="shop-total-label">Shop Subtotal:</span>
                                        <PriceDisplay :price="cart.total" class="shop-total-value" />
                                    </div>
                                    <div v-if="!canCheckout(cart)" class="min-order-warning">
                                        Add <PriceDisplay :price="getRemainingAmount(cart)" class="remaining-amount" />
                                        more to reach the minimum order amount of
                                        <PriceDisplay :price="cart.shop?.min_order_amount" class="min-amount" />
                                    </div>
                                </div>
                                <div class="shop-footer-actions">
                                    <Button
                                        v-if="canCheckout(cart)"
                                        class="btn-primary-modern"
                                        as-child
                                    >
                                        <Link :href="`/checkout?shop_id=${cart.shop_id}`">
                                            <ShoppingCart :size="20" />
                                            Proceed to Checkout
                                        </Link>
                                    </Button>
                                    <template v-else>
                                        <Button
                                            class="btn-continue-shopping"
                                            as-child
                                        >
                                            <Link :href="`/shops/${cart.shop_id}`">
                                                <Store :size="18" />
                                                Continue Shopping
                                            </Link>
                                        </Button>
                                        <Button
                                            class="btn-primary-modern"
                                            disabled
                                        >
                                            <ShoppingCart :size="20" />
                                            Proceed to Checkout
                                        </Button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<style scoped>
/* Cart Page */
.cart-page {
    background: var(--smo-bg);
    min-height: 100vh;
    font-family: var(--font-body);
    padding-bottom: 2rem;
}

/* Loading State */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
}

.loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--smo-border);
    border-top-color: var(--smo-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.loading-text {
    margin-top: 1rem;
    color: var(--smo-text-secondary);
    font-family: var(--font-body);
    font-size: 0.9375rem;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
    text-align: center;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.1) 0%,
        rgba(44, 95, 93, 0.05) 100%);
    color: var(--smo-primary);
}

.empty-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--smo-text-primary);
    margin-top: 1rem;
}

.empty-subtitle {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--smo-text-secondary);
    margin-top: 0.375rem;
}

/* Cart Content */
.cart-content {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Cart Header */
.cart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.cart-title {
    font-family: var(--font-display);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.cart-subtitle {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--smo-text-secondary);
    margin-top: 0.125rem;
}

.btn-clear-all {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    background: var(--smo-surface);
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-text-secondary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.btn-clear-all:hover:not(:disabled) {
    border-color: #DC2626;
    color: #DC2626;
    background: rgba(220, 38, 38, 0.05);
}

.btn-clear-all:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Shops List */
.shops-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Shop Card */
.shop-card {
    background: var(--smo-surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--smo-border);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: all var(--transition-base);
}

.shop-card:hover {
    box-shadow: var(--shadow-lg);
}

/* Shop Header */
.shop-header {
    padding: 1rem 1.25rem;
    border-bottom: 2px solid var(--smo-border);
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.03) 0%,
        rgba(44, 95, 93, 0.01) 100%);
}

.shop-info {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.shop-icon {
    color: var(--smo-primary);
}

.shop-name {
    font-family: var(--font-display);
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.shop-items {
    font-family: var(--font-body);
    font-size: 0.8125rem;
    color: var(--smo-text-secondary);
    margin-top: 0.0625rem;
}

/* Shop Items List */
.shop-items-list {
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.875rem;
}

/* Cart Item */
.cart-item {
    display: flex;
    gap: 1rem;
    padding: 0.875rem;
    background: var(--smo-bg);
    border: 1px solid var(--smo-border);
    border-radius: var(--radius-md);
    transition: all var(--transition-base);
}

.cart-item:hover {
    border-color: var(--smo-primary-light);
    box-shadow: var(--shadow-sm);
}

/* Item Image */
.item-image-link {
    flex-shrink: 0;
}

.item-image {
    width: 72px;
    height: 72px;
    border-radius: var(--radius-sm);
    overflow: hidden;
    background: white;
    border: 1px solid var(--smo-border);
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-base);
}

.item-image:hover img {
    transform: scale(1.05);
}

.item-image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-size: 2rem;
}

/* Item Details */
.item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
}

.item-header {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 0.75rem;
}

.item-name {
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-text-primary);
    transition: color var(--transition-base);
}

.item-name:hover {
    color: var(--smo-primary);
}

.btn-remove {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--smo-border);
    background: var(--smo-surface);
    color: var(--smo-text-muted);
    cursor: pointer;
    transition: all var(--transition-base);
    flex-shrink: 0;
}

.btn-remove:hover:not(:disabled) {
    border-color: #DC2626;
    color: #DC2626;
    background: rgba(220, 38, 38, 0.05);
}

.btn-remove:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Item Footer */
.item-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: auto;
    flex-wrap: wrap;
}

/* Quantity Controls */
.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.qty-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: var(--radius-sm);
    border: 2px solid var(--smo-border);
    background: var(--smo-surface);
    color: var(--smo-text-primary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.qty-btn:hover:not(:disabled) {
    border-color: var(--smo-primary);
    color: var(--smo-primary);
}

.qty-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.qty-input {
    width: 56px;
    height: 28px;
    text-align: center;
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-sm);
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0;
}

/* Item Pricing */
.item-pricing {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.125rem;
}

.item-unit-price {
    font-size: 0.8125rem;
    color: var(--smo-text-muted);
}

.item-subtotal {
    font-family: var(--font-display);
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

/* Shop Footer */
.shop-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.05) 0%,
        rgba(44, 95, 93, 0.02) 100%);
    border-top: 2px solid var(--smo-border);
    flex-wrap: wrap;
}

.shop-total-section {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.shop-total {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.shop-total-label {
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-text-secondary);
}

.shop-total-value {
    font-family: var(--font-display);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--smo-primary);
}

.min-order-warning {
    font-family: var(--font-body);
    font-size: 0.8125rem;
    color: #DC2626;
    padding: 0.375rem 0.625rem;
    background: rgba(220, 38, 38, 0.05);
    border: 1px solid rgba(220, 38, 38, 0.2);
    border-radius: var(--radius-sm);
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.min-order-warning .remaining-amount,
.min-order-warning .min-amount {
    font-weight: 600;
    color: #DC2626;
}

.shop-footer-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-continue-shopping {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, #6EAA4B 0%, #7AB85D 100%);
    border: 2px solid #5D9440;
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 700;
    color: white;
    cursor: pointer;
    transition: all var(--transition-base);
    box-shadow: 0 2px 8px rgba(110, 170, 75, 0.2);
}

.btn-continue-shopping:hover {
    background: linear-gradient(135deg, #7AB85D 0%, #88C56B 100%);
    border-color: #6EAA4B;
    box-shadow: 0 4px 12px rgba(110, 170, 75, 0.3);
    transform: translateY(-1px);
}

.btn-continue-shopping:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(110, 170, 75, 0.2);
}

/* Responsive */
@media (max-width: 768px) {
    .cart-item {
        flex-direction: column;
        gap: 0.75rem;
    }

    .item-image {
        width: 100%;
        height: 160px;
    }

    .item-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .item-pricing {
        align-items: flex-start;
        flex-direction: row;
        justify-content: space-between;
    }

    .shop-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .shop-total-section {
        width: 100%;
    }

    .shop-total {
        justify-content: space-between;
    }

    .min-order-warning {
        font-size: 0.75rem;
    }

    .shop-footer-actions {
        width: 100%;
        flex-direction: column;
    }

    .btn-continue-shopping {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .cart-header {
        flex-direction: column;
        align-items: stretch;
    }

    .btn-clear-all {
        justify-content: center;
    }
}
</style>
