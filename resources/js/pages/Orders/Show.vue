<script setup lang="ts">
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import { Badge } from '@/components/ui/badge';
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
import type { Order, OrderStatus } from '@/types/api';
import { router, Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CalendarDays,
    MapPin,
    Package,
    Phone,
    ShoppingBag,
    Store,
    X,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    order: Order;
}

const props = defineProps<Props>();
const { getLocalizedName } = useLocale();
const cancelling = ref(false);

function getStatusBadgeVariant(status: OrderStatus) {
    const variants: Record<
        OrderStatus,
        'default' | 'secondary' | 'destructive' | 'outline'
    > = {
        pending: 'secondary',
        confirmed: 'default',
        processing: 'default',
        shipped: 'default',
        delivered: 'default',
        cancelled: 'destructive',
        refunded: 'outline',
    };
    return variants[status] || 'default';
}

function formatStatus(status: OrderStatus): string {
    return status.charAt(0).toUpperCase() + status.slice(1);
}

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

function canCancelOrder(): boolean {
    return ['pending', 'confirmed'].includes(props.order.status);
}

async function handleCancelOrder() {
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }

    cancelling.value = true;
    try {
        router.post(
            `/api/v1/orders/${props.order.id}/cancel`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    alert('Order cancelled successfully');
                    router.reload();
                },
                onError: () => {
                    alert('Failed to cancel order. Please try again.');
                },
                onFinish: () => {
                    cancelling.value = false;
                },
            },
        );
    } catch (error) {
        console.error('Failed to cancel order:', error);
        cancelling.value = false;
    }
}
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />

    <ShopLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Back Button -->
            <div class="mb-6">
                <Button variant="ghost" as-child>
                    <Link href="/orders">
                        <ArrowLeft :size="16" />
                        Back to Orders
                    </Link>
                </Button>
            </div>

            <!-- Page Header -->
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold">
                            Order #{{ order.order_number }}
                        </h1>
                        <Badge :variant="getStatusBadgeVariant(order.status)">
                            {{ formatStatus(order.status) }}
                        </Badge>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-muted-foreground">
                        <CalendarDays :size="16" />
                        <span>{{ formatDate(order.created_at) }}</span>
                    </div>
                </div>

                <!-- Cancel Button -->
                <Button
                    v-if="canCancelOrder()"
                    variant="destructive"
                    @click="handleCancelOrder"
                    :disabled="cancelling"
                >
                    <X :size="16" />
                    {{ cancelling ? 'Cancelling...' : 'Cancel Order' }}
                </Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Shop Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Store :size="20" />
                                Shop
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div
                                class="flex items-center gap-3 rounded-lg border p-3"
                            >
                                <Store :size="20" class="text-primary" />
                                <div>
                                    <div class="font-semibold">
                                        {{ order.shop?.name }}
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ order.items_count }}
                                        {{
                                            order.items_count === 1
                                                ? 'item'
                                                : 'items'
                                        }}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Order Items -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Package :size="20" />
                                Order Items
                            </CardTitle>
                            <CardDescription>
                                {{ order.items_count }} item{{
                                    order.items_count === 1 ? '' : 's'
                                }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="flex items-start gap-4 rounded-lg border p-4"
                            >
                                <!-- Product Image Placeholder -->
                                <div
                                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-md bg-muted"
                                >
                                    <Package
                                        :size="24"
                                        class="text-muted-foreground"
                                    />
                                </div>

                                <!-- Product Info -->
                                <div class="flex flex-1 flex-col">
                                    <div class="font-medium">
                                        {{ getProductName(item) }}
                                    </div>
                                    <div class="mt-1 flex items-center gap-4 text-sm text-muted-foreground">
                                        <span>Qty: {{ item.quantity }}</span>
                                        <span>·</span>
                                        <PriceDisplay :price="item.price" />
                                        <span>each</span>
                                    </div>
                                </div>

                                <!-- Subtotal -->
                                <div class="text-right">
                                    <PriceDisplay
                                        :price="getItemSubtotal(item)"
                                        class="text-lg font-semibold"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Delivery Information -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <MapPin :size="20" />
                                Delivery Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Address -->
                            <div>
                                <div class="mb-2 text-sm font-medium">
                                    Address
                                </div>
                                <div
                                    class="space-y-1 rounded-lg border p-3"
                                >
                                    <div class="font-medium">
                                        {{ order.delivery_city?.name }}
                                    </div>
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
                                            >Entry:
                                            {{ order.delivery_entry }}</span
                                        >
                                        <span
                                            v-if="order.delivery_floor"
                                            class="ml-3"
                                            >Floor:
                                            {{ order.delivery_floor }}</span
                                        >
                                        <span
                                            v-if="order.delivery_apartment"
                                            class="ml-3"
                                            >Apt:
                                            {{ order.delivery_apartment }}</span
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
                                    <Phone :size="14" />
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
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Order Summary -->
                    <Card class="sticky top-4">
                        <CardHeader>
                            <CardTitle>Order Summary</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="text-muted-foreground">
                                    Subtotal
                                </span>
                                <PriceDisplay :price="order.subtotal" />
                            </div>

                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span class="text-muted-foreground">
                                    Shipping
                                </span>
                                <span class="text-sm">Included</span>
                            </div>

                            <Separator />

                            <div
                                class="flex items-center justify-between"
                            >
                                <span class="text-lg font-semibold">
                                    Total
                                </span>
                                <PriceDisplay
                                    :price="order.total"
                                    class="text-xl font-bold"
                                />
                            </div>

                            <Separator />

                            <!-- Actions -->
                            <div class="space-y-2">
                                <Button class="w-full" as-child>
                                    <Link href="/orders">
                                        <ShoppingBag :size="18" />
                                        View All Orders
                                    </Link>
                                </Button>
                                <Button variant="outline" class="w-full" as-child>
                                    <Link href="/products">
                                        Continue Shopping
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
