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
import { ShoppingCart } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PriceDisplay from './PriceDisplay.vue';
import LoginModal from '@/components/LoginModal.vue';
import QuantityControl from './QuantityControl.vue';

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
const unitName = computed(() => {
    const unit = props.product.nomenclature?.unit;
    if (!unit) return '';
    const locale = (window as any).locale || 'ru';
    return locale === 'kz' ? unit.shortname_kz : unit.shortname_ru;
});
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

async function handleQuantityChange(newQuantity: number) {
    if (!cartItem.value || adding.value) return;

    adding.value = true;
    try {
        await cartStore.updateQuantity(cartItem.value.id, {
            quantity: newQuantity,
        });
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
                            {{ t({ ru: 'Нет в наличии', kz: 'Қолда жоқ' }) }}
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
                <div class="flex items-baseline gap-1">
                    <PriceDisplay :price="product.price" class="text-lg" />
                    <span v-if="unitName" class="text-xs text-muted-foreground">/ {{ unitName }}</span>
                </div>
                <span v-if="product.shop" class="text-xs text-muted-foreground">
                    {{ product.shop.name }}
                </span>
            </div>

            <!-- Availability -->
            <div class="mt-3">
                <div
                    :class="[
                        'inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md border text-xs font-semibold',
                        isOutOfStock
                            ? 'bg-gradient-to-br from-red-50 to-red-100 border-red-300 text-red-700'
                            : 'bg-gradient-to-br from-lime-100 to-lime-200 border-lime-300 text-lime-700'
                    ]"
                >
                    <span :class="['w-1.5 h-1.5 rounded-full', isOutOfStock ? 'bg-red-600' : 'bg-lime-600']" />
                    <span v-if="isOutOfStock">
                        {{ t({ ru: 'Нет в наличии', kz: 'Қолжетімсіз' }) }}
                    </span>
                    <span v-else>
                        {{ t({
                            ru: `В наличии: ${product.quantity} ${unitName}`,
                            kz: `Қолжетімді: ${product.quantity} ${unitName}`,
                        }) }}
                    </span>
                </div>
            </div>
        </CardContent>

        <CardFooter class="p-4 pt-0">
            <!-- Show quantity controls when product is in cart -->
            <QuantityControl
                v-if="isInCart"
                :model-value="cartQuantity"
                @update:model-value="handleQuantityChange"
                @remove="handleRemoveFromCart"
                :disabled="adding"
                :editable="false"
                :show-remove="true"
                class="w-full"
            />

            <!-- Show add to cart button when product is not in cart -->
            <button
                v-else
                @click="handleAddToCart"
                :disabled="isOutOfStock || adding"
                class="w-full font-display font-semibold px-4 py-2 bg-amber-500 text-white rounded-lg hover:cursor-pointer hover:bg-amber-600 transition-all duration-200 disabled:bg-concrete-300 disabled:text-concrete-500 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
                <ShoppingCart :size="16" />
                <span v-if="adding">{{ t({ ru: 'Добавление...', kz: 'Қосылуда...' }) }}</span>
                <span v-else-if="isOutOfStock">{{ t({ ru: 'Нет в наличии', kz: 'Қолда жоқ' }) }}</span>
                <span v-else>{{ t({ ru: 'В корзину', kz: 'Себетке' }) }}</span>
            </button>
        </CardFooter>
    </Card>

    <LoginModal v-model:open="showLoginModal" />
</template>
