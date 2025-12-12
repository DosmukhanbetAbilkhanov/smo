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
import { Check, ShoppingCart } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PriceDisplay from './PriceDisplay.vue';

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
const showSuccess = ref(false);

async function handleAddToCart() {
    if (isOutOfStock.value || adding.value) return;

    adding.value = true;
    showSuccess.value = false;

    try {
        await cartStore.addItem({
            product_id: props.product.id,
            quantity: 1,
        });

        // Show success feedback
        showSuccess.value = true;
        setTimeout(() => {
            showSuccess.value = false;
        }, 2000);
    } catch (error) {
        console.error('Failed to add to cart:', error);
        alert('Failed to add item to cart. Please try again.');
    } finally {
        adding.value = false;
    }
}
</script>

<template>
    <Card class="group overflow-hidden transition-shadow hover:shadow-lg">
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
            <Button
                @click="handleAddToCart"
                :disabled="isOutOfStock || adding"
                :variant="showSuccess ? 'default' : 'default'"
                class="w-full gap-2"
                size="sm"
            >
                <Check v-if="showSuccess" :size="16" />
                <ShoppingCart v-else :size="16" />
                <span v-if="showSuccess">Added!</span>
                <span v-else-if="adding">Adding...</span>
                <span v-else-if="isOutOfStock">Out of Stock</span>
                <span v-else>Add to Cart</span>
            </Button>
        </CardFooter>
    </Card>
</template>
