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
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Order, OrderStatus } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Package, ShoppingBag, Store } from 'lucide-vue-next';

interface Props {
    orders: {
        data: Order[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

const props = defineProps<Props>();

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

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="My Orders" />

    <ShopLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold">My Orders</h1>
                <p class="mt-2 text-muted-foreground">
                    Track and manage your orders
                </p>
            </div>

            <!-- Empty State -->
            <div
                v-if="orders.data.length === 0"
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <div
                    class="flex h-24 w-24 items-center justify-center rounded-full bg-muted"
                >
                    <ShoppingBag :size="48" class="text-muted-foreground" />
                </div>
                <h2 class="mt-6 text-2xl font-semibold">
                    No orders yet
                </h2>
                <p class="mt-2 text-muted-foreground">
                    Start shopping to place your first order
                </p>
                <Button class="mt-6" as-child>
                    <Link href="/products">Browse Products</Link>
                </Button>
            </div>

            <!-- Orders List -->
            <div v-else class="space-y-4">
                <Card
                    v-for="order in orders.data"
                    :key="order.id"
                    class="overflow-hidden transition-shadow hover:shadow-md"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <CardTitle class="text-lg">
                                        Order #{{ order.order_number }}
                                    </CardTitle>
                                    <Badge
                                        :variant="
                                            getStatusBadgeVariant(order.status)
                                        "
                                    >
                                        {{ formatStatus(order.status) }}
                                    </Badge>
                                </div>
                                <CardDescription class="mt-1">
                                    Placed on {{ formatDate(order.created_at) }}
                                </CardDescription>
                            </div>
                            <PriceDisplay
                                :price="order.total"
                                class="text-xl font-bold"
                            />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <!-- Shop Info -->
                            <div
                                class="flex items-center gap-2 rounded-lg bg-muted p-3"
                            >
                                <Store :size="18" class="text-muted-foreground" />
                                <div>
                                    <div class="font-medium">
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

                            <!-- Quick Items Preview -->
                            <div
                                v-if="order.items && order.items.length > 0"
                                class="space-y-2"
                            >
                                <div class="flex items-center gap-2 text-sm font-medium">
                                    <Package :size="14" />
                                    <span>Items</span>
                                </div>
                                <div class="space-y-1">
                                    <div
                                        v-for="item in order.items.slice(0, 3)"
                                        :key="item.id"
                                        class="flex items-center justify-between text-sm"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="text-muted-foreground"
                                                >×{{ item.quantity }}</span
                                            >
                                            <span>{{
                                                item.product?.name_en ||
                                                item.product?.name_ru ||
                                                'Product'
                                            }}</span>
                                        </div>
                                        <PriceDisplay :price="item.subtotal" />
                                    </div>
                                    <p
                                        v-if="order.items.length > 3"
                                        class="text-sm text-muted-foreground"
                                    >
                                        +{{ order.items.length - 3 }} more
                                        {{
                                            order.items.length - 3 === 1
                                                ? 'item'
                                                : 'items'
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <Button variant="outline" class="w-full" as-child>
                                <Link :href="`/orders/${order.id}`">
                                    View Details
                                    <ChevronRight :size="16" class="ml-auto" />
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pagination -->
                <div
                    v-if="orders.last_page > 1"
                    class="mt-8 flex items-center justify-center gap-2"
                >
                    <Button
                        variant="outline"
                        :disabled="orders.current_page === 1"
                        as-child
                    >
                        <Link
                            :href="`/orders?page=${orders.current_page - 1}`"
                            >Previous</Link
                        >
                    </Button>
                    <div class="text-sm text-muted-foreground">
                        Page {{ orders.current_page }} of {{ orders.last_page }}
                    </div>
                    <Button
                        variant="outline"
                        :disabled="orders.current_page === orders.last_page"
                        as-child
                    >
                        <Link
                            :href="`/orders?page=${orders.current_page + 1}`"
                            >Next</Link
                        >
                    </Button>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
