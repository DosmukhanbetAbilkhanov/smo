<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
} from '@/components/ui/card';
import { useLocale } from '@/composables/useLocale';
import { useCartStore } from '@/stores/cart';
import type { Product } from '@/types/api';
import { Link } from '@inertiajs/vue3';
import { ShoppingCart, Minus, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PriceDisplay from './PriceDisplay.vue';
import LoginModal from '@/components/LoginModal.vue';

interface Props {
    product: Product;
}

const props = defineProps<Props>();
const { getLocalizedName, t } = useLocale();
const cartStore = useCartStore();

const productName = computed(() => getLocalizedName(props.product));
const productImage = computed(
    () => props.product.images?.[0] || null,
);
const isOutOfStock = computed(() => props.product.quantity === 0);
const adding = ref(false);
const showLoginModal = ref(false);

// Find cart item for this product
const cartItem = computed(() => {
    return cartStore.items.find((item) => item.product_id === props.product.id);
});

const isInCart = computed(() => !!cartItem.value);
const cartQuantity = computed(() => cartItem.value?.quantity || 0);

async function handleAddToCart() {
    if (isOutOfStock.value || adding.value) return;

    adding.value = true;

    try {
        await cartStore.addItem({
            product_id: props.product.id,
            quantity: 1,
        });
    } catch (error: any) {
        console.error('Failed to add to cart:', error);

        // Check if error is due to authentication (401 Unauthorized or 419 Session Expired)
        const status = error?.status;
        if (status === 401 || status === 419) {
            showLoginModal.value = true;
        } else {
            alert(t({ ru: 'Не удалось добавить товар в корзину. Попробуйте еще раз.', kz: 'Тауарды себетке қосу мүмкін болмады. Қайталап көріңіз.' }));
        }
    } finally {
        adding.value = false;
    }
}

async function handleIncreaseQuantity() {
    if (!cartItem.value || adding.value) return;

    adding.value = true;

    try {
        await cartStore.updateQuantity(cartItem.value.id, {
            quantity: cartItem.value.quantity + 1,
        });
    } catch (error: any) {
        console.error('Failed to increase quantity:', error);
        alert(t({ ru: 'Не удалось обновить количество. Попробуйте еще раз.', kz: 'Санын жаңарту мүмкін болмады. Қайталап көріңіз.' }));
    } finally {
        adding.value = false;
    }
}

async function handleDecreaseQuantity() {
    if (!cartItem.value || adding.value) return;

    adding.value = true;

    try {
        if (cartItem.value.quantity === 1) {
            await cartStore.removeItem(cartItem.value.id);
        } else {
            await cartStore.updateQuantity(cartItem.value.id, {
                quantity: cartItem.value.quantity - 1,
            });
        }
    } catch (error: any) {
        console.error('Failed to decrease quantity:', error);
        alert(t({ ru: 'Не удалось обновить количество. Попробуйте еще раз.', kz: 'Санын жаңарту мүмкін болмады. Қайталап көріңіз.' }));
    } finally {
        adding.value = false;
    }
}

async function handleRemoveFromCart() {
    if (!cartItem.value || adding.value) return;

    adding.value = true;

    try {
        await cartStore.removeItem(cartItem.value.id);
    } catch (error: any) {
        console.error('Failed to remove from cart:', error);
        alert(t({ ru: 'Не удалось удалить товар из корзины. Попробуйте еще раз.', kz: 'Тауарды себеттен жою мүмкін болмады. Қайталап көріңіз.' }));
    } finally {
        adding.value = false;
    }
}

</script>

<template>
    <div class="group bg-white rounded-2xl border border-concrete-200 overflow-hidden transition-all duration-300 hover:shadow-industrial-lg hover:-translate-y-1">
        <Link :href="`/products/${product.id}`" class="block">
            <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-concrete-100 to-concrete-50">
                <img
                    v-if="productImage"
                    :src="productImage"
                    :alt="productName"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                />
                <div
                    v-else
                    class="flex h-full w-full items-center justify-center"
                >
                    <span class="text-6xl opacity-20">📦</span>
                </div>
                <div
                    v-if="isOutOfStock"
                    class="absolute inset-0 flex items-center justify-center bg-steel-900/80 backdrop-blur-sm"
                >
                    <span class="font-display text-sm font-bold text-white px-4 py-2 bg-rust-600 rounded-lg">
                        {{ t({ ru: 'Нет в наличии', kz: 'Қолда жоқ' }) }}
                    </span>
                </div>
            </div>
        </Link>

        <div class="p-4">
            <Link :href="`/products/${product.id}`">
                <h3 class="font-display line-clamp-2 text-base font-semibold text-steel-900 transition-colors duration-200 hover:text-amber-600 mb-3">
                    {{ productName }}
                </h3>
            </Link>

            <div class="flex items-center justify-between mb-4">
                <PriceDisplay :price="product.price" class="font-display text-xl font-bold text-steel-900" />
                <span v-if="product.shop" class="font-body text-xs text-concrete-600 px-2 py-1 bg-concrete-100 rounded-md">
                    {{ product.shop.name }}
                </span>
            </div>

            <!-- Show quantity controls when product is in cart -->
            <div v-if="isInCart" class="flex w-full gap-2">
                <button
                    @click="handleDecreaseQuantity"
                    :disabled="adding || cartQuantity <= 1"
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white border-2 border-concrete-300 text-steel-700 rounded-lg transition-all duration-200 hover:border-steel-700 hover:bg-concrete-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Minus :size="16" />
                </button>

                <div class="flex flex-1 items-center justify-center rounded-lg border-2 border-concrete-300 bg-concrete-50 px-3 font-display text-base font-bold text-steel-900">
                    {{ cartQuantity }}
                </div>

                <button
                    @click="handleIncreaseQuantity"
                    :disabled="adding"
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white border-2 border-concrete-300 text-steel-700 rounded-lg transition-all duration-200 hover:border-steel-700 hover:bg-concrete-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Plus :size="16" />
                </button>

                <button
                    @click="handleRemoveFromCart"
                    :disabled="adding"
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-rust-500 text-white rounded-lg transition-all duration-200 hover:bg-rust-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Trash2 :size="16" />
                </button>
            </div>

            <!-- Show add to cart button when product is not in cart -->
            <button
                v-else
                @click="handleAddToCart"
                :disabled="isOutOfStock || adding"
                class="w-full flex items-center justify-center gap-2 font-display font-bold px-6 py-3 bg-amber-500 text-white rounded-lg transition-all duration-200 hover:bg-amber-600 hover:shadow-industrial-md hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
                <ShoppingCart :size="18" />
                <span v-if="adding">{{ t({ ru: 'Добавление...', kz: 'Қосылуда...' }) }}</span>
                <span v-else-if="isOutOfStock">{{ t({ ru: 'Нет в наличии', kz: 'Қолда жоқ' }) }}</span>
                <span v-else>{{ t({ ru: 'В корзину', kz: 'Себетке' }) }}</span>
            </button>
        </div>
    </div>

    <LoginModal v-model:open="showLoginModal" />
</template>
