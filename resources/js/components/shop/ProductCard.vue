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
import { ShoppingCart } from 'lucide-vue-next';
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

</script>

<template>
    <Card class="group overflow-hidden transition-shadow hover:shadow-md">
        <Link :href="`/products/${product.id}`" class="block">
            <CardHeader class="p-0">
                <div class="relative aspect-[2/1] overflow-hidden bg-muted rounded-t-md">
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

        <CardContent class="px-3">
            <Link :href="`/products/${product.id}`">
                <h3
                    class="line-clamp-2 text-sm font-medium transition-colors hover:text-primary"
                >
                    {{ productName }}
                </h3>
            </Link>

            <div class="mt-1 flex items-center justify-between">
                <PriceDisplay :price="product.price" class="text-base font-semibold" />
                <span v-if="product.shop" class="text-xs text-muted-foreground">
                    {{ product.shop.name }}
                </span>
            </div>
        </CardContent>

     
    </Card>

    <LoginModal v-model:open="showLoginModal" />
</template>
