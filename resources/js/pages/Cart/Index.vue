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
const { getLocalizedName, t } = useLocale();
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
        <div class="min-h-screen pb-8">
            <!-- Progress Indicator -->
            <CheckoutProgress :current-step="1" />

            <div class="max-w-5xl mx-auto px-4 py-6">
                <!-- Page Header -->
                <div class="text-center mb-8 animate-fadeInUp">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t({ ru: 'Корзина покупок', kz: 'Сатып алу себеті' }) }}</h1>
                    <p class="text-[15px] text-gray-600">
                        {{ t({ ru: 'Проверьте товары и перейдите к оформлению', kz: 'Тауарларды тексеріп, рәсімдеуге өтіңіз' }) }}
                    </p>
                </div>

                <!-- Loading State -->
                <div
                    v-if="cartStore.loading && cartStore.carts.length === 0"
                    class="flex flex-col items-center justify-center py-8"
                >
                    <div class="w-12 h-12 border-4 border-gray-200 border-t-[#2C5F5D] rounded-full animate-spin"></div>
                    <p class="mt-4 text-gray-600 text-[15px]">{{ t({ ru: 'Загрузка корзины...', kz: 'Себет жүктелуде...' }) }}</p>
                </div>

                <!-- Empty State -->
                <div
                    v-else-if="cartStore.isEmpty"
                    class="flex flex-col items-center justify-center py-8 text-center animate-fadeIn"
                >
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-[#2C5F5D]/10 to-[#2C5F5D]/5 text-[#2C5F5D]">
                        <ShoppingBag :size="48" />
                    </div>
                    <h2 class="mt-4 text-2xl font-bold text-gray-900">{{ t({ ru: 'Ваша корзина пуста', kz: 'Себетіңіз бос' }) }}</h2>
                    <p class="mt-1.5 text-[15px] text-gray-600">
                        {{ t({ ru: 'Добавьте товары для начала', kz: 'Бастау үшін тауарларды қосыңыз' }) }}
                    </p>
                    <Button class="btn-primary-modern mt-6" as-child>
                        <Link href="/products">{{ t({ ru: 'Смотреть товары', kz: 'Тауарларды қарау' }) }}</Link>
                    </Button>
                </div>

                <!-- Cart Content -->
                <div v-else class="flex flex-col gap-5">
                    <!-- Header with Clear All Button -->
                    <div class="flex items-center justify-between flex-wrap gap-4 animate-fadeInUp">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ t({ ru: 'Ваша корзина', kz: 'Сіздің себетіңіз' }) }}</h2>
                            <p class="text-sm text-gray-600 mt-0.5">
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
                    <div class="flex flex-col gap-5">
                        <div
                            v-for="(cart, index) in cartsWithItems"
                            :key="cart.id"
                            class="bg-white rounded-lg border border-gray-300 shadow-md overflow-hidden transition-all hover:shadow-lg animate-fadeInUp"
                            :style="{ animationDelay: `${index * 100}ms` }"
                        >
                            <!-- Shop Header -->
                            <div class="px-5 py-4 border-b-2 border-gray-300 bg-gradient-to-br from-[#2C5F5D]/3 to-[#2C5F5D]/1">
                                <div class="flex items-center gap-2.5">
                                    <Store :size="20" class="text-[#2C5F5D]" />
                                    <div>
                                        <h3 class="text-[17px] font-bold text-gray-900">{{ cart.shop?.name }}</h3>
                                        <p class="text-[13px] text-gray-600 mt-0.5">
                                            {{ cart.items_count }} {{ t({ ru: cart.items_count === 1 ? 'товар' : 'товаров', kz: 'тауар' }) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Items -->
                            <div class="divide-y divide-gray-200">
                                <div
                                    v-for="item in cart.items"
                                    :key="item.id"
                                    class="flex gap-4 p-4 transition-all hover:bg-gray-50"
                                >
                                    <!-- Product Image -->
                                    <Link
                                        :href="`/products/${item.product_id}`"
                                        class="flex-shrink-0"
                                    >
                                        <div class="w-18 h-18 rounded-md overflow-hidden bg-white border border-gray-200">
                                            <img
                                                v-if="item.product?.images?.[0]"
                                                :src="item.product.images[0]"
                                                :alt="getProductName(item)"
                                                class="w-full h-full object-cover transition-transform hover:scale-105"
                                            />
                                            <div v-else class="flex items-center justify-center w-full h-full text-3xl">
                                                <span>📦</span>
                                            </div>
                                        </div>
                                    </Link>

                                    <!-- Product Info -->
                                    <div class="flex-1 flex flex-col gap-2.5">
                                        <div class="flex items-start justify-between gap-3">
                                            <Link :href="`/products/${item.product_id}`">
                                                <h4 class="text-[15px] font-semibold text-gray-900 transition-colors hover:text-[#2C5F5D]">
                                                    {{ getProductName(item) }}
                                                </h4>
                                            </Link>
                                            <button
                                                @click="handleRemoveItem(item.id)"
                                                :disabled="removing === item.id"
                                                class="flex items-center justify-center w-7 h-7 rounded-sm border border-gray-300 bg-white text-gray-400 transition-all hover:border-red-600 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0"
                                            >
                                                <X :size="16" />
                                            </button>
                                        </div>

                                        <div class="flex items-center justify-between gap-3 mt-auto flex-wrap">
                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-1.5">
                                                <button
                                                    @click="handleUpdateQuantity(item.id, item.quantity - 1)"
                                                    :disabled="item.quantity <= 1 || updating === item.id"
                                                    class="flex items-center justify-center w-7 h-7 rounded-sm border-2 border-gray-300 bg-white text-gray-900 transition-all hover:border-[#2C5F5D] hover:text-[#2C5F5D] disabled:opacity-50 disabled:cursor-not-allowed"
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
                                                    class="w-14 h-7 text-center border-2 border-gray-300 rounded-sm font-semibold text-sm p-0"
                                                    min="1"
                                                />
                                                <button
                                                    @click="handleUpdateQuantity(item.id, item.quantity + 1)"
                                                    :disabled="updating === item.id"
                                                    class="flex items-center justify-center w-7 h-7 rounded-sm border-2 border-gray-300 bg-white text-gray-900 transition-all hover:border-[#2C5F5D] hover:text-[#2C5F5D] disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    <Plus :size="14" />
                                                </button>
                                            </div>

                                            <!-- Item Pricing -->
                                            <div class="flex flex-col items-end gap-0.5">
                                                <PriceDisplay
                                                    :price="item.price"
                                                    class="text-[13px] text-gray-500"
                                                />
                                                <PriceDisplay
                                                    :price="getItemSubtotal(item)"
                                                    class="text-[17px] font-bold text-gray-900"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Footer -->
                            <div class="flex items-center justify-between gap-4 px-5 py-4 bg-gradient-to-br from-[#2C5F5D]/5 to-[#2C5F5D]/2 border-t-2 border-gray-300 flex-wrap">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-[15px] font-semibold text-gray-600">{{ t({ ru: 'Промежуточный итог:', kz: 'Аралық қорытынды:' }) }}</span>
                                        <PriceDisplay :price="cart.total" class="text-xl font-bold text-[#2C5F5D]" />
                                    </div>
                                    <div v-if="!canCheckout(cart)" class="text-[13px] text-red-600 px-2.5 py-1.5 bg-red-50 border border-red-200 rounded-sm inline-flex items-center gap-1 flex-wrap">
                                        {{ t({ ru: 'Добавьте еще', kz: 'Тағы қосыңыз' }) }} <PriceDisplay :price="getRemainingAmount(cart)" class="font-semibold text-red-600" />
                                        {{ t({ ru: 'для достижения минимальной суммы заказа', kz: 'тапсырыстың ең аз сомасына жету үшін' }) }}
                                        <PriceDisplay :price="cart.shop?.min_order_amount" class="font-semibold text-red-600" />
                                    </div>
                                </div>
                                <div class="flex gap-3 flex-wrap">
                                    <Button
                                        v-if="canCheckout(cart)"
                                        class="btn-primary-modern"
                                        as-child
                                    >
                                        <Link :href="`/checkout?shop_id=${cart.shop_id}`">
                                            <ShoppingCart :size="20" />
                                            {{ t({ ru: 'Перейти к оформлению', kz: 'Рәсімдеуге өту' }) }}
                                        </Link>
                                    </Button>
                                    <template v-else>
                                        <Button
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-[#6EAA4B] to-[#7AB85D] border-2 border-[#5D9440] rounded-md text-[15px] font-bold text-white transition-all hover:from-[#7AB85D] hover:to-[#88C56B] hover:border-[#6EAA4B] hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 active:shadow-md"
                                            as-child
                                        >
                                            <Link :href="`/shops/${cart.shop_id}`">
                                                <Store :size="18" />
                                                {{ t({ ru: 'Продолжить покупки', kz: 'Сатып алуды жалғастыру' }) }}
                                            </Link>
                                        </Button>
                                        <Button
                                            class="btn-primary-modern"
                                            disabled
                                        >
                                            <ShoppingCart :size="20" />
                                            {{ t({ ru: 'Перейти к оформлению', kz: 'Рәсімдеуге өту' }) }}
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
/* Animations */
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
    animation: fadeInUp 0.6s ease-out forwards;
}

.animate-fadeIn {
    animation: fadeIn 0.4s ease-out forwards;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .flex.gap-4.p-4 {
        flex-direction: column;
        gap: 0.75rem;
    }

    .w-18.h-18 {
        width: 100%;
        height: 160px;
    }
}

@media (max-width: 640px) {
    .flex.items-center.justify-between.flex-wrap.gap-4 {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
