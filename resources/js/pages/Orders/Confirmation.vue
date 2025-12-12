<script setup lang="ts">
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Order } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, MapPin, Package, Phone, ShoppingBag, Store } from 'lucide-vue-next';

interface Props {
    order: Order;
}

const props = defineProps<Props>();
const { getLocalizedName } = useLocale();

function getProductName(item: any) {
    return item.product ? getLocalizedName(item.product) : 'Product';
}

function getItemSubtotal(item: any) {
    return item.price * item.quantity;
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head :title="`Order ${order.order_number} Confirmed`" />

    <ShopLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Success Message -->
            <div
                class="mb-8 flex flex-col items-center justify-center text-center"
            >
                <div
                    class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20"
                >
                    <CheckCircle2 :size="48" class="text-green-600 dark:text-green-500" />
                </div>
                <h1 class="text-3xl font-bold">Order Confirmed!</h1>
                <p class="mt-2 text-lg text-muted-foreground">
                    Thank you for your order
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Order #{{ order.order_number }}
                </p>
            </div>

            <div class="mx-auto max-w-3xl space-y-6">
                <!-- Order Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Order Details</CardTitle>
                        <CardDescription>
                            Placed on {{ formatDate(order.created_at) }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Shop Info -->
                        <div>
                            <div class="mb-2 text-sm font-medium">Shop</div>
                            <div
                                class="flex items-center gap-2 rounded-lg border p-3"
                            >
                                <Store :size="18" class="text-primary" />
                                <span class="font-medium">{{
                                    order.shop?.name
                                }}</span>
                            </div>
                        </div>

                        <Separator />

                        <!-- Items -->
                        <div>
                            <div class="mb-3 flex items-center gap-2 text-sm font-medium">
                                <Package :size="16" />
                                <span>Items ({{ order.items_count }})</span>
                            </div>
                            <div class="space-y-3">
                                <div
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="flex items-start justify-between rounded-lg border p-3"
                                >
                                    <div class="flex-1">
                                        <div class="font-medium">
                                            {{ getProductName(item) }}
                                        </div>
                                        <div class="mt-1 text-sm text-muted-foreground">
                                            <PriceDisplay :price="item.price" />
                                            × {{ item.quantity }}
                                        </div>
                                    </div>
                                    <PriceDisplay
                                        :price="getItemSubtotal(item)"
                                        class="font-semibold"
                                    />
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <!-- Delivery Info -->
                        <div>
                            <div class="mb-3 flex items-center gap-2 text-sm font-medium">
                                <MapPin :size="16" />
                                <span>Delivery Address</span>
                            </div>
                            <div class="rounded-lg border p-3 space-y-1">
                                <div>{{ order.delivery_city?.name }}</div>
                                <div>{{ order.delivery_address }}</div>
                                <div
                                    v-if="
                                        order.delivery_entry ||
                                        order.delivery_floor ||
                                        order.delivery_apartment ||
                                        order.delivery_intercom
                                    "
                                    class="text-sm text-muted-foreground"
                                >
                                    <span v-if="order.delivery_entry"
                                        >Entry: {{ order.delivery_entry }}</span
                                    >
                                    <span
                                        v-if="order.delivery_floor"
                                        class="ml-3"
                                        >Floor: {{ order.delivery_floor }}</span
                                    >
                                    <span
                                        v-if="order.delivery_apartment"
                                        class="ml-3"
                                        >Apt: {{ order.delivery_apartment }}</span
                                    >
                                    <span
                                        v-if="order.delivery_intercom"
                                        class="ml-3"
                                        >Intercom:
                                        {{ order.delivery_intercom }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Contact Phone -->
                        <div>
                            <div class="mb-2 flex items-center gap-2 text-sm font-medium">
                                <Phone :size="16" />
                                <span>Contact Phone</span>
                            </div>
                            <div class="rounded-lg border p-3">
                                {{ order.contact_phone }}
                            </div>
                        </div>

                        <!-- Delivery Notes -->
                        <div v-if="order.delivery_notes">
                            <div class="mb-2 text-sm font-medium">
                                Delivery Notes
                            </div>
                            <div class="rounded-lg border p-3 text-sm">
                                {{ order.delivery_notes }}
                            </div>
                        </div>

                        <Separator />

                        <!-- Total -->
                        <div class="flex items-center justify-between text-lg">
                            <span class="font-semibold">Total</span>
                            <PriceDisplay
                                :price="order.total"
                                class="text-xl font-bold"
                            />
                        </div>
                    </CardContent>
                </Card>

                <!-- Next Steps -->
                <Card>
                    <CardHeader>
                        <CardTitle>What's Next?</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <p>
                            1. The shop will review your order and contact you
                            to confirm the details.
                        </p>
                        <p>
                            2. You will receive updates about your order status
                            via phone or email.
                        </p>
                        <p>
                            3. You can track your order status in your orders
                            page.
                        </p>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <div class="flex gap-3">
                    <Button size="lg" as-child class="flex-1">
                        <Link :href="`/orders/${order.id}`">
                            View Order Details
                        </Link>
                    </Button>
                    <Button variant="outline" size="lg" as-child class="flex-1">
                        <Link href="/orders">
                            <ShoppingBag :size="18" />
                            My Orders
                        </Link>
                    </Button>
                </div>

                <div class="text-center">
                    <Button variant="link" as-child>
                        <Link href="/">Continue Shopping</Link>
                    </Button>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
