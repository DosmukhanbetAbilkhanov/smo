<script setup lang="ts">
import CheckoutProgress from '@/components/checkout/CheckoutProgress.vue';
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import { Input } from '@/components/ui/input';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { useCartStore } from '@/stores/cart';
import type { CartItem } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { Minus, Plus, ShoppingBag, ShoppingCart, Store, Trash2, X } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const cartStore = useCartStore();
const { getLocalizedName, t } = useLocale();
const removing = ref<number | null>(null);
const updating = ref<number | null>(null);

const cartsWithItems = computed(() => {
    return cartStore.carts.filter(cart => cart.items && cart.items.length > 0);
});

function getProductName(item: CartItem) {
    return item.product ? getLocalizedName(item.product) : 'Product';
}

function getUnitShortname(item: CartItem) {
    const unit = item.product?.nomenclature?.unit;
    if (!unit) return '';
    const locale = (window as any).locale || 'ru';
    return locale === 'kz' ? unit.shortname_kz : unit.shortname_ru;
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
    if (!confirm(t({ ru: 'Вы уверены, что хотите очистить корзину?', kz: 'Себетті тазалағыңыз келетініне сенімдісіз бе?' }))) return;

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
    <Head :title="t({ ru: 'Корзина покупок', kz: 'Сатып алу себеті' })" />

    <ShopLayout>
        <div class="min-h-screen pb-8 bg-concrete-50">
            <!-- Progress Indicator -->
            <CheckoutProgress :current-step="1" />

            <div class="max-w-5xl mx-auto px-4 py-6">
                <!-- Page Header -->
                <div class="text-center mb-8 animate-fadeInUp">
                    <h1 class="font-display text-4xl font-bold text-steel-900 mb-2">{{ t({ ru: 'Корзина покупок', kz: 'Сатып алу себеті' }) }}</h1>
                    <p class="font-body text-base text-concrete-600">
                        {{ t({ ru: 'Проверьте товары и перейдите к оформлению', kz: 'Тауарларды тексеріп, рәсімдеуге өтіңіз' }) }}
                    </p>
                </div>

                <!-- Loading State -->
                <div
                    v-if="cartStore.loading && cartStore.carts.length === 0"
                    class="flex flex-col items-center justify-center py-12"
                >
                    <div class="w-12 h-12 border-4 border-concrete-200 border-t-steel-700 rounded-full animate-spin"></div>
                    <p class="mt-4 font-body text-concrete-600">{{ t({ ru: 'Загрузка корзины...', kz: 'Себет жүктелуде...' }) }}</p>
                </div>

                <!-- Empty State -->
                <div
                    v-else-if="cartStore.isEmpty"
                    class="flex flex-col items-center justify-center py-12 text-center animate-fadeIn"
                >
                    <div class="flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-steel-100 to-steel-50 text-steel-700 shadow-industrial-md">
                        <ShoppingBag :size="40" :stroke-width="2" />
                    </div>
                    <h2 class="mt-6 font-display text-3xl font-bold text-steel-900">{{ t({ ru: 'Ваша корзина пуста', kz: 'Себетіңіз бос' }) }}</h2>
                    <p class="mt-2 font-body text-base text-concrete-600">
                        {{ t({ ru: 'Добавьте товары для начала', kz: 'Бастау үшін тауарларды қосыңыз' }) }}
                    </p>
                    <button
                        @click="$inertia.visit('/products')"
                        class="font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl hover:bg-amber-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5 mt-6"
                    >
                        {{ t({ ru: 'Смотреть товары', kz: 'Тауарларды қарау' }) }}
                    </button>
                </div>

                <!-- Cart Content -->
                <div v-else class="flex flex-col gap-6">
                    <!-- Header with Clear All Button -->
                    <div class="flex items-center justify-between flex-wrap gap-4 animate-fadeInUp">
                        <div>
                            <h2 class="font-display text-2xl font-bold text-steel-900">{{ t({ ru: 'Ваша корзина', kz: 'Сіздің себетіңіз' }) }}</h2>
                            <p class="font-body text-sm text-concrete-600 mt-1">
                                {{ cartStore.itemsCount }} {{ t({ ru: cartStore.itemsCount === 1 ? 'товар' : 'товаров', kz: 'тауар' }) }}
                                {{ t({ ru: 'из', kz: '' }) }} {{ cartsWithItems.length }} {{ t({ ru: cartsWithItems.length === 1 ? 'магазина' : 'магазинов', kz: 'дүкеннен' }) }}
                            </p>
                        </div>
                        <!-- <button
                            @click="handleClearCart"
                            :disabled="cartStore.loading"
                            class="flex items-center gap-1.5 px-4 py-2 bg-white border-2 border-gray-300 rounded-md text-sm font-semibold text-gray-600 transition-all hover:border-red-600 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <Trash2 :size="16" />
                            {{ t({ ru: 'Очистить все', kz: 'Барлығын тазалау' }) }}
                        </button> -->
                    </div>

                    <!-- Cart Items Grouped by Shop -->
                    <div class="flex flex-col gap-6">
                        <div
                            v-for="(cart, index) in cartsWithItems"
                            :key="cart.id"
                            class="bg-white rounded-2xl border border-concrete-200 shadow-industrial-md overflow-hidden transition-all hover:shadow-industrial-lg animate-fadeInUp"
                            :style="{ animationDelay: `${index * 100}ms` }"
                        >
                            <!-- Shop Header -->
                            <div class="px-6 py-4 border-b-2 border-concrete-200 bg-gradient-to-br from-steel-50 to-steel-25">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-steel-600 to-steel-700 rounded-lg text-white shadow-industrial-sm">
                                        <Store :size="20" :stroke-width="2.5" />
                                    </div>
                                    <div>
                                        <h3 class="font-display text-lg font-bold text-steel-900">{{ cart.shop?.name }}</h3>
                                        <p class="font-body text-sm text-concrete-600 mt-0.5">
                                            {{ cart.items_count }} {{ t({ ru: cart.items_count === 1 ? 'товар' : 'товаров', kz: 'тауар' }) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Items -->
                            <div class="divide-y divide-concrete-200">
                                <div
                                    v-for="item in cart.items"
                                    :key="item.id"
                                    class="flex gap-4 p-5 transition-all hover:bg-concrete-50"
                                >
                                    <!-- Product Image -->
                                    <Link
                                        :href="`/products/${item.product_id}`"
                                        class="flex-shrink-0"
                                    >
                                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-white border-2 border-concrete-200 shadow-industrial-sm hover:shadow-industrial-md transition-shadow">
                                            <img
                                                v-if="item.product?.images?.[0]"
                                                :src="item.product.images[0]"
                                                :alt="getProductName(item)"
                                                class="w-full h-full object-cover transition-transform hover:scale-110"
                                            />
                                            <div v-else class="flex items-center justify-center w-full h-full text-3xl">
                                                <span>📦</span>
                                            </div>
                                        </div>
                                    </Link>

                                    <!-- Product Info -->
                                    <div class="flex-1 flex flex-col gap-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <Link :href="`/products/${item.product_id}`">
                                                <h4 class="font-display text-base font-semibold text-steel-900 transition-colors hover:text-amber-600">
                                                    {{ getProductName(item) }}
                                                </h4>
                                            </Link>
                                            <button
                                                @click="handleRemoveItem(item.id)"
                                                :disabled="removing === item.id"
                                                class="flex items-center justify-center w-8 h-8 font-display font-bold bg-transparent text-rust-600 border-2 border-rust-600 rounded-lg hover:bg-rust-600 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-rust-600 flex-shrink-0"
                                            >
                                                <X :size="18" />
                                            </button>
                                        </div>

                                        <div class="flex items-center justify-between gap-3 mt-auto flex-wrap">
                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-2">
                                                <button
                                                    @click="handleUpdateQuantity(item.id, item.quantity - 1)"
                                                    :disabled="item.quantity <= 1 || updating === item.id"
                                                    class="flex items-center justify-center w-8 h-8 font-display font-bold bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-steel-700"
                                                >
                                                    <Minus :size="16" />
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
                                                    class="font-display w-16 h-8 text-center border-2 border-concrete-300 rounded-lg font-semibold text-sm p-0 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                                                    min="1"
                                                />
                                                <button
                                                    @click="handleUpdateQuantity(item.id, item.quantity + 1)"
                                                    :disabled="updating === item.id"
                                                    class="flex items-center justify-center w-8 h-8 font-display font-bold bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-steel-700"
                                                >
                                                    <Plus :size="16" />
                                                </button>
                                            </div>

                                            <!-- Item Pricing -->
                                            <div class="flex flex-col items-end gap-1">
                                                <div class="flex items-baseline gap-1">
                                                    <PriceDisplay
                                                        :price="item.price"
                                                        class="font-body text-sm text-concrete-500"
                                                    />
                                                    <span v-if="getUnitShortname(item)" class="font-body text-xs text-concrete-400">
                                                        / {{ getUnitShortname(item) }}
                                                    </span>
                                                </div>
                                                <PriceDisplay
                                                    :price="getItemSubtotal(item)"
                                                    class="font-display text-lg font-bold text-steel-900"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Footer -->
                            <div class="flex items-center justify-between gap-4 px-6 py-5 bg-gradient-to-br from-steel-50 to-concrete-50 border-t-2 border-concrete-200 flex-wrap">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-3">
                                        <span class="font-display font-semibold text-steel-700">{{ t({ ru: 'Промежуточный итог:', kz: 'Аралық қорытынды:' }) }}</span>
                                        <PriceDisplay :price="cart.total" class="font-display text-2xl font-bold text-steel-900" />
                                    </div>
                                    <div v-if="!canCheckout(cart)" class="font-body text-sm text-rust-700 px-3 py-2 bg-rust-50 border-l-4 border-rust-500 rounded-lg inline-flex items-center gap-1 flex-wrap">
                                        {{ t({ ru: 'Добавьте еще', kz: 'Тағы қосыңыз' }) }} <PriceDisplay :price="getRemainingAmount(cart)" class="font-semibold text-rust-700" />
                                        {{ t({ ru: 'для достижения минимальной суммы заказа', kz: 'тапсырыстың ең аз сомасына жету үшін' }) }}
                                        <PriceDisplay :price="cart.shop?.min_order_amount" class="font-semibold text-rust-700" />
                                    </div>
                                </div>
                                <div class="flex gap-3 flex-wrap">
                                    <button
                                        v-if="canCheckout(cart)"
                                        @click="$inertia.visit(`/checkout?shop_id=${cart.shop_id}`)"
                                        class="font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl hover:bg-amber-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center gap-2"
                                    >
                                        <ShoppingCart :size="20" />
                                        {{ t({ ru: 'Перейти к оформлению', kz: 'Рәсімдеуге өту' }) }}
                                    </button>
                                    <template v-else>
                                        <button
                                            class="font-display font-bold px-6 py-3 bg-rust-500 text-white rounded-lg hover:bg-rust-600 hover:shadow-industrial-md transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center gap-2"
                                            @click="$inertia.visit(`/shops/${cart.shop_id}`)"
                                        >
                                            <Store :size="18" />
                                            {{ t({ ru: 'Продолжить покупки', kz: 'Сатып алуды жалғастыру' }) }}
                                        </button>
                                        <button
                                            disabled
                                            class="font-display font-bold px-8 py-4 bg-concrete-300 text-concrete-500 rounded-xl cursor-not-allowed inline-flex items-center gap-2"
                                        >
                                            <ShoppingCart :size="20" />
                                            {{ t({ ru: 'Перейти к оформлению', kz: 'Рәсімдеуге өту' }) }}
                                        </button>
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
/* Industrial Refined Design System Animations */
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

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.animate-fadeIn {
    animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
</style>
