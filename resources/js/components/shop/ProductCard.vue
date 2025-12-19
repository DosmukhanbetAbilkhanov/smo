<script setup lang="ts">
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
import { Minus, Plus, ShoppingCart, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PriceDisplay from './PriceDisplay.vue';
import LoginModal from '@/components/LoginModal.vue';

interface Props {
    product: Product;
}

const props = defineProps<Props>();
const { getLocalizedName } = useLocale();
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
            alert('Failed to add item to cart. Please try again.');
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
        console.error('Failed to update quantity:', error);
        alert('Failed to update quantity. Please try again.');
    } finally {
        adding.value = false;
    }
}

async function handleDecreaseQuantity() {
    if (!cartItem.value || adding.value) return;

    adding.value = true;
    try {
        if (cartItem.value.quantity > 1) {
            await cartStore.updateQuantity(cartItem.value.id, {
                quantity: cartItem.value.quantity - 1,
            });
        }
    } catch (error: any) {
        console.error('Failed to update quantity:', error);
        alert('Failed to update quantity. Please try again.');
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
        alert('Failed to remove item from cart. Please try again.');
    } finally {
        adding.value = false;
    }
}
</script>

<template>
    <Card class="group overflow-hidden transition-shadow hover:shadow-lg card-modern">
        <Link :href="`/products/${product.id}`" class="block">
            <CardHeader class="p-0">
                <div class="relative aspect-square overflow-hidden bg-muted">
                    <img
                        v-if="productImage"
                        :src="productImage"
                        :alt="productName"
                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                    />
                    <div
                        v-else
                        class="flex h-full w-full items-center justify-center bg-muted"
                    >
                        <span class="text-4xl text-muted-foreground/20">📦</span>
                    </div>
                    <div
                        v-if="isOutOfStock"
                        class="absolute inset-0 flex items-center justify-center bg-background/80"
                    >
                        <span class="text-sm font-medium text-muted-foreground">
                            Out of Stock
                        </span>
                    </div>
                </div>
            </CardHeader>
        </Link>

        <CardContent class="p-4">
            <Link :href="`/products/${product.id}`">
                <h3
                    class="line-clamp-2 text-sm font-medium transition-colors hover:text-primary"
                >
                    {{ productName }}
                </h3>
            </Link>

            <div class="mt-2 flex items-center justify-between">
                <PriceDisplay :price="product.price" class="text-lg" />
                <span v-if="product.shop" class="text-xs text-muted-foreground">
                    {{ product.shop.name }}
                </span>
            </div>
        </CardContent>

        <CardFooter class="p-4 pt-0">
            <!-- Show quantity controls when product is in cart -->
            <div v-if="isInCart" class="flex w-full gap-2">
                <button
                    @click="handleDecreaseQuantity"
                    :disabled="adding || cartQuantity <= 1"
                    class="flex items-center justify-center flex-shrink-0 w-8 h-8 font-display font-bold bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-steel-700"
                >
                    <Minus :size="16" />
                </button>

                <div class="flex flex-1 items-center justify-center rounded-lg border-2 border-concrete-300 bg-white px-3 text-sm font-display font-semibold text-steel-900">
                    {{ cartQuantity }}
                </div>

                <button
                    @click="handleIncreaseQuantity"
                    :disabled="adding"
                    class="flex items-center justify-center flex-shrink-0 w-8 h-8 font-display font-bold bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-steel-700"
                >
                    <Plus :size="16" />
                </button>

                <button
                    @click="handleRemoveFromCart"
                    :disabled="adding"
                    class="flex items-center justify-center flex-shrink-0 w-8 h-8 font-display font-bold bg-transparent text-rust-600 border-2 border-rust-600 rounded-lg hover:bg-rust-600 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-rust-600"
                >
                    <Trash2 :size="16" />
                </button>
            </div>

            <!-- Show add to cart button when product is not in cart -->
            <button
                v-else
                @click="handleAddToCart"
                :disabled="isOutOfStock || adding"
                class="w-full font-display font-semibold px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all duration-200 disabled:bg-concrete-300 disabled:text-concrete-500 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
                <ShoppingCart :size="16" />
                <span v-if="adding">Adding...</span>
                <span v-else-if="isOutOfStock">Out of Stock</span>
                <span v-else>Add to Cart</span>
            </button>
        </CardFooter>
    </Card>

    <LoginModal v-model:open="showLoginModal" />
</template>
