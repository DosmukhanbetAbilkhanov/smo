<script setup lang="ts">
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Cart, City } from '@/types/api';
import { Form, Head, Link } from '@inertiajs/vue3';
import { MapPin, Package, Phone, ShoppingBag, Store } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    carts: Cart[];
    cities: City[];
    defaultPhone?: string;
}

const props = defineProps<Props>();
const { getLocalizedName } = useLocale();

// Select the first shop by default
const selectedShopId = ref<number>(props.carts[0]?.shop_id || 0);
const selectedCart = computed(() =>
    props.carts.find((cart) => cart.shop_id === selectedShopId.value),
);

function getProductName(item: any) {
    return item.product ? getLocalizedName(item.product) : 'Product';
}

function getItemSubtotal(item: any) {
    return item.price * item.quantity;
}
</script>

<template>
    <Head title="Checkout" />

    <ShopLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold">Checkout</h1>
                <p class="mt-2 text-muted-foreground">
                    Complete your order details
                </p>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Delivery Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Shop Selection (if multiple shops) -->
                    <Card v-if="carts.length > 1">
                        <CardHeader>
                            <CardTitle>Select Shop</CardTitle>
                            <CardDescription>
                                Choose which shop to order from
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <button
                                v-for="cart in carts"
                                :key="cart.id"
                                @click="selectedShopId = cart.shop_id"
                                type="button"
                                class="flex w-full items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted"
                                :class="{
                                    'border-primary bg-primary/5':
                                        selectedShopId === cart.shop_id,
                                }"
                            >
                                <div class="flex items-center gap-3">
                                    <Store
                                        :size="20"
                                        :class="
                                            selectedShopId === cart.shop_id
                                                ? 'text-primary'
                                                : 'text-muted-foreground'
                                        "
                                    />
                                    <div class="text-left">
                                        <div class="font-medium">
                                            {{ cart.shop?.name }}
                                        </div>
                                        <div class="text-sm text-muted-foreground">
                                            {{ cart.items_count }}
                                            {{
                                                cart.items_count === 1
                                                    ? 'item'
                                                    : 'items'
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <PriceDisplay
                                    :price="cart.total"
                                    class="font-semibold"
                                />
                            </button>
                        </CardContent>
                    </Card>

                    <!-- Delivery Form -->
                    <Form
                        v-if="selectedCart"
                        action="/checkout"
                        method="post"
                        #default="{ processing, errors }"
                    >
                        <input
                            type="hidden"
                            name="shop_id"
                            :value="selectedShopId"
                        />

                        <Card>
                            <CardHeader>
                                <div class="flex items-center gap-2">
                                    <MapPin :size="20" class="text-primary" />
                                    <CardTitle>Delivery Information</CardTitle>
                                </div>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- City Selection -->
                                <div class="space-y-2">
                                    <Label for="delivery_city_id"
                                        >City *</Label
                                    >
                                    <select
                                        id="delivery_city_id"
                                        name="delivery_city_id"
                                        required
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <option value="">Select a city</option>
                                        <option
                                            v-for="city in cities"
                                            :key="city.id"
                                            :value="city.id"
                                        >
                                            {{ city.name }}
                                        </option>
                                    </select>
                                    <p
                                        v-if="errors.delivery_city_id"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors.delivery_city_id }}
                                    </p>
                                </div>

                                <!-- Address -->
                                <div class="space-y-2">
                                    <Label for="delivery_address"
                                        >Street Address *</Label
                                    >
                                    <Input
                                        id="delivery_address"
                                        name="delivery_address"
                                        type="text"
                                        required
                                        placeholder="Street name, house number"
                                    />
                                    <p
                                        v-if="errors.delivery_address"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors.delivery_address }}
                                    </p>
                                </div>

                                <!-- Apartment Details Grid -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="delivery_entry">Entry</Label>
                                        <Input
                                            id="delivery_entry"
                                            name="delivery_entry"
                                            type="text"
                                            placeholder="e.g., 2"
                                        />
                                        <p
                                            v-if="errors.delivery_entry"
                                            class="text-sm text-destructive"
                                        >
                                            {{ errors.delivery_entry }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="delivery_floor">Floor</Label>
                                        <Input
                                            id="delivery_floor"
                                            name="delivery_floor"
                                            type="text"
                                            placeholder="e.g., 5"
                                        />
                                        <p
                                            v-if="errors.delivery_floor"
                                            class="text-sm text-destructive"
                                        >
                                            {{ errors.delivery_floor }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="delivery_apartment"
                                            >Apartment</Label
                                        >
                                        <Input
                                            id="delivery_apartment"
                                            name="delivery_apartment"
                                            type="text"
                                            placeholder="e.g., 23"
                                        />
                                        <p
                                            v-if="errors.delivery_apartment"
                                            class="text-sm text-destructive"
                                        >
                                            {{ errors.delivery_apartment }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="delivery_intercom"
                                            >Intercom</Label
                                        >
                                        <Input
                                            id="delivery_intercom"
                                            name="delivery_intercom"
                                            type="text"
                                            placeholder="e.g., 123"
                                        />
                                        <p
                                            v-if="errors.delivery_intercom"
                                            class="text-sm text-destructive"
                                        >
                                            {{ errors.delivery_intercom }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Contact Phone -->
                                <div class="space-y-2">
                                    <Label for="contact_phone"
                                        >Contact Phone *</Label
                                    >
                                    <div class="flex items-center gap-2">
                                        <Phone
                                            :size="18"
                                            class="text-muted-foreground"
                                        />
                                        <Input
                                            id="contact_phone"
                                            name="contact_phone"
                                            type="tel"
                                            required
                                            :defaultValue="defaultPhone"
                                            placeholder="+7 (XXX) XXX-XX-XX"
                                            class="flex-1"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.contact_phone"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors.contact_phone }}
                                    </p>
                                </div>

                                <!-- Delivery Notes -->
                                <div class="space-y-2">
                                    <Label for="delivery_notes"
                                        >Delivery Notes (Optional)</Label
                                    >
                                    <textarea
                                        id="delivery_notes"
                                        name="delivery_notes"
                                        rows="3"
                                        placeholder="Any special instructions for delivery..."
                                        class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    ></textarea>
                                    <p
                                        v-if="errors.delivery_notes"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors.delivery_notes }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Submit Buttons -->
                        <div class="flex gap-3">
                            <Button
                                type="submit"
                                size="lg"
                                class="flex-1"
                                :disabled="processing"
                            >
                                <ShoppingBag :size="20" />
                                {{ processing ? 'Processing...' : 'Place Order' }}
                            </Button>
                            <Button
                                variant="outline"
                                size="lg"
                                as-child
                                :disabled="processing"
                            >
                                <Link href="/cart">Cancel</Link>
                            </Button>
                        </div>
                    </Form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-4" v-if="selectedCart">
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <Package :size="20" class="text-primary" />
                                <CardTitle>Order Summary</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Shop Info -->
                            <div class="rounded-lg bg-muted p-3">
                                <div class="flex items-center gap-2">
                                    <Store
                                        :size="16"
                                        class="text-muted-foreground"
                                    />
                                    <div class="font-medium">
                                        {{ selectedCart.shop?.name }}
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ selectedCart.items_count }}
                                    {{
                                        selectedCart.items_count === 1
                                            ? 'item'
                                            : 'items'
                                    }}
                                </p>
                            </div>

                            <!-- Items List -->
                            <div class="space-y-3">
                                <div
                                    v-for="item in selectedCart.items"
                                    :key="item.id"
                                    class="flex items-start justify-between text-sm"
                                >
                                    <div class="flex-1">
                                        <div class="font-medium">
                                            {{ getProductName(item) }}
                                        </div>
                                        <div class="text-muted-foreground">
                                            Qty: {{ item.quantity }}
                                        </div>
                                    </div>
                                    <PriceDisplay
                                        :price="getItemSubtotal(item)"
                                        class="font-medium"
                                    />
                                </div>
                            </div>

                            <Separator />

                            <!-- Totals -->
                            <div class="space-y-2">
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="text-muted-foreground">
                                        Subtotal
                                    </span>
                                    <PriceDisplay :price="selectedCart.total" />
                                </div>
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="text-muted-foreground">
                                        Shipping
                                    </span>
                                    <span class="text-sm">
                                        Calculated after order
                                    </span>
                                </div>
                            </div>

                            <Separator />

                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold">
                                    Total
                                </span>
                                <PriceDisplay
                                    :price="selectedCart.total"
                                    class="text-xl"
                                />
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
