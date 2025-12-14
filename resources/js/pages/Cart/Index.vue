<script setup lang="ts">
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { useCartStore } from '@/stores/cart';
import type { CartItem } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { Minus, Plus, ShoppingBag, Store, Trash2, X } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

const cartStore = useCartStore();
const { getLocalizedName } = useLocale();
const removing = ref<number | null>(null);
const updating = ref<number | null>(null);

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
</script>

<template>
    <Head title="Shopping Cart" />

    <ShopLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold">Shopping Cart</h1>
                <p class="mt-2 text-muted-foreground">
                    Review your items and proceed to checkout
                </p>
            </div>

            <!-- Loading State -->
            <div
                v-if="cartStore.loading && cartStore.carts.length === 0"
                class="flex items-center justify-center py-12"
            >
                <div class="text-center">
                    <div
                        class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-primary border-t-transparent"
                    ></div>
                    <p class="mt-4 text-muted-foreground">Loading cart...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="cartStore.isEmpty"
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <div
                    class="flex h-24 w-24 items-center justify-center rounded-full bg-muted"
                >
                    <ShoppingBag :size="48" class="text-muted-foreground" />
                </div>
                <h2 class="mt-6 text-2xl font-semibold">
                    Your cart is empty
                </h2>
                <p class="mt-2 text-muted-foreground">
                    Add some products to get started
                </p>
                <Button class="mt-6" as-child>
                    <Link href="/products">Browse Products</Link>
                </Button>
            </div>

            <!-- Cart Content -->
            <div v-else>
                <!-- Cart Items Grouped by Shop -->
                <div class="space-y-6">
                    <!-- Header with Clear All Button -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold">Your Cart</h2>
                            <p class="text-sm text-muted-foreground">
                                {{ cartStore.itemsCount }} {{ cartStore.itemsCount === 1 ? 'item' : 'items' }}
                                from {{ cartStore.carts.length }} {{ cartStore.carts.length === 1 ? 'shop' : 'shops' }}
                            </p>
                        </div>
                        <Button
                            variant="outline"
                            size="sm"
                            @click="handleClearCart"
                            :disabled="cartStore.loading"
                        >
                            <Trash2 :size="16" class="mr-2" />
                            Clear All
                        </Button>
                    </div>
                    <div
                        v-for="cart in cartStore.carts"
                        :key="cart.id"
                    >
                        <Card>
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <Store :size="20" class="text-primary" />
                                        <div>
                                            <CardTitle>{{ cart.shop?.name }}</CardTitle>
                                            <p class="text-sm text-muted-foreground">
                                                {{ cart.items_count }} {{ cart.items_count === 1 ? 'item' : 'items' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div
                                    v-for="item in cart.items"
                                    :key="item.id"
                                    class="flex gap-4 rounded-lg border p-4"
                                >
                                <!-- Product Image -->
                                <Link
                                    :href="`/products/${item.product_id}`"
                                    class="shrink-0"
                                >
                                    <div
                                        class="h-24 w-24 overflow-hidden rounded-md bg-muted"
                                    >
                                        <img
                                            v-if="item.product?.images?.[0]"
                                            :src="item.product.images[0]"
                                            :alt="getProductName(item)"
                                            class="h-full w-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex h-full w-full items-center justify-center"
                                        >
                                            <span class="text-2xl">📦</span>
                                        </div>
                                    </div>
                                </Link>

                                <!-- Product Info -->
                                <div class="flex flex-1 flex-col">
                                    <div
                                        class="flex items-start justify-between"
                                    >
                                        <div>
                                            <Link
                                                :href="`/products/${item.product_id}`"
                                            >
                                                <h3
                                                    class="font-medium hover:text-primary"
                                                >
                                                    {{ getProductName(item) }}
                                                </h3>
                                            </Link>
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            @click="handleRemoveItem(item.id)"
                                            :disabled="removing === item.id"
                                        >
                                            <X :size="16" />
                                        </Button>
                                    </div>

                                    <div
                                        class="mt-auto flex items-center justify-between"
                                    >
                                        <!-- Quantity Controls -->
                                        <div
                                            class="flex items-center gap-2"
                                        >
                                            <Button
                                                variant="outline"
                                                size="icon"
                                                class="h-8 w-8"
                                                @click="
                                                    handleUpdateQuantity(
                                                        item.id,
                                                        item.quantity - 1,
                                                    )
                                                "
                                                :disabled="
                                                    item.quantity <= 1 ||
                                                    updating === item.id
                                                "
                                            >
                                                <Minus :size="14" />
                                            </Button>
                                            <Input
                                                type="number"
                                                :value="item.quantity"
                                                @input="
                                                    (e: Event) =>
                                                        handleUpdateQuantity(
                                                            item.id,
                                                            parseInt(
                                                                (
                                                                    e.target as HTMLInputElement
                                                                ).value,
                                                            ),
                                                        )
                                                "
                                                class="h-8 w-16 text-center"
                                                min="1"
                                            />
                                            <Button
                                                variant="outline"
                                                size="icon"
                                                class="h-8 w-8"
                                                @click="
                                                    handleUpdateQuantity(
                                                        item.id,
                                                        item.quantity + 1,
                                                    )
                                                "
                                                :disabled="updating === item.id"
                                            >
                                                <Plus :size="14" />
                                            </Button>
                                        </div>

                                        <!-- Item Subtotal -->
                                        <div class="text-right">
                                            <PriceDisplay
                                                :price="item.price"
                                                class="text-sm text-muted-foreground"
                                            />
                                            <PriceDisplay
                                                :price="getItemSubtotal(item)"
                                                class="text-lg"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="border-t bg-muted/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Shop Subtotal:</span>
                                <PriceDisplay :price="cart.total" class="text-lg font-semibold" />
                            </div>
                            <Button as-child>
                                <Link :href="`/checkout?shop_id=${cart.shop_id}`">
                                    Proceed to Checkout
                                </Link>
                            </Button>
                        </CardFooter>
                    </Card>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
