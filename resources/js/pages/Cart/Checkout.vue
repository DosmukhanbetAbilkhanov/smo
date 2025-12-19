<script setup lang="ts">
import CheckoutProgress from '@/components/checkout/CheckoutProgress.vue';
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Cart } from '@/types/api';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ChevronDown, MapPin, Package, ShoppingBag, Store } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    carts: Cart[];
}

const props = defineProps<Props>();
const { getLocalizedName, t } = useLocale();

// Select the first shop by default
const selectedShopId = ref<number>(props.carts[0]?.shop_id || 0);
const selectedCart = computed(() =>
    props.carts.find((cart) => cart.shop_id === selectedShopId.value),
);

// Collapsible state for items list
const isItemsOpen = ref(true);

// Delivery form fields
const deliveryAddress = ref('');
const deliveryEntry = ref('');
const deliveryFloor = ref('');
const deliveryApartment = ref('');
const deliveryIntercom = ref('');

// Computed formatted address
const formattedDeliveryAddress = computed(() => {
    const parts: string[] = [];

    if (deliveryAddress.value) {
        parts.push(deliveryAddress.value);
    }

    const details: string[] = [];
    if (deliveryEntry.value) {
        details.push(`${t({ ru: 'Подъезд', kz: 'Кіреберіс' })} ${deliveryEntry.value}`);
    }
    if (deliveryFloor.value) {
        details.push(`${t({ ru: 'Этаж', kz: 'Қабат' })} ${deliveryFloor.value}`);
    }
    if (deliveryApartment.value) {
        details.push(`${t({ ru: 'Кв.', kz: 'Пәт.' })} ${deliveryApartment.value}`);
    }
    if (deliveryIntercom.value) {
        details.push(`${t({ ru: 'Домофон', kz: 'Домофон' })} ${deliveryIntercom.value}`);
    }

    if (details.length > 0) {
        parts.push(details.join(', '));
    }

    return parts.join(' • ');
});

function getProductName(item: any) {
    return item.product ? getLocalizedName(item.product) : 'Product';
}

function getItemSubtotal(item: any) {
    return item.price * item.quantity;
}
</script>

<template>
    <Head :title="t({ ru: 'Оформление заказа', kz: 'Тапсырысты рәсімдеу' })" />

    <ShopLayout>
        <div class="checkout-page">
            <!-- Progress Indicator -->
            <CheckoutProgress :current-step="2" />

            <div class="container mx-auto px-4 py-12">
                <!-- Page Header -->
                <div class="text-center mb-12">
                    <h1 class="font-display text-4xl md:text-5xl font-bold text-steel-900 mb-3 tracking-tight">{{ t({ ru: 'Безопасное оформление', kz: 'Қауіпсіз рәсімдеу' }) }}</h1>
                    <p class="font-body text-lg text-concrete-600 font-medium">
                        {{ t({ ru: 'Заполните информацию о доставке для завершения заказа', kz: 'Тапсырысты аяқтау үшін жеткізу туралы ақпаратты толтырыңыз' }) }}
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 lg:gap-12">
                    <!-- Order Summary (appears first on mobile) -->
                    <div class="order-1 lg:order-2">
                        <div class="bg-white rounded-2xl p-8 shadow-industrial-xl border border-concrete-200 sticky top-4 backdrop-blur-sm bg-gradient-to-br from-white/95 to-concrete-50/95" v-if="selectedCart">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-steel-600 to-steel-700 rounded-xl text-white shadow-industrial-sm">
                                    <Package :size="24" :stroke-width="2.5" />
                                </div>
                                <h2 class="font-display text-2xl font-bold text-steel-900">{{ t({ ru: 'Сводка заказа', kz: 'Тапсырыс қорытындысы' }) }}</h2>
                            </div>

                            <!-- Shop Info -->
                            <div class="bg-gradient-to-br from-steel-100/50 to-steel-50/30 rounded-xl p-5 mb-6 border border-steel-200/50">
                                <div class="flex items-start gap-4">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-steel-600 to-steel-700 rounded-lg text-white shadow-industrial-sm flex-shrink-0">
                                        <Store :size="20" :stroke-width="2.5" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-display text-lg font-semibold text-steel-900 mb-1">{{ selectedCart.shop?.name }}</div>
                                        <div class="font-body text-sm text-concrete-600 font-medium">
                                            {{ selectedCart.items_count }}
                                            {{ t({ ru: selectedCart.items_count === 1 ? 'товар' : 'товаров', kz: 'тауар' }) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Shop Location -->
                                <div v-if="selectedCart.shop?.city || selectedCart.shop?.address" class="flex items-start gap-3 mt-4 pt-4 border-t border-steel-200/50">
                                    <MapPin :size="16" class="text-concrete-500 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1 min-w-0">
                                        <div v-if="selectedCart.shop?.city" class="font-body text-sm font-semibold text-steel-900 mb-0.5">
                                            {{ selectedCart.shop.city.name }}
                                        </div>
                                        <div v-if="selectedCart.shop?.address" class="font-body text-sm text-concrete-600">
                                            {{ selectedCart.shop.address }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Address Preview -->
                            <div v-if="formattedDeliveryAddress" class="bg-gradient-to-br from-amber-100/40 to-amber-50/30 rounded-xl p-5 mb-6 border border-amber-200/50">
                                <div class="flex items-start gap-4">
                                    <MapPin :size="20" class="text-amber-600 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <div class="font-display text-sm font-semibold text-steel-900 mb-1">{{ t({ ru: 'Адрес доставки', kz: 'Жеткізу мекенжайы' }) }}</div>
                                        <div class="font-body text-sm text-concrete-700 leading-relaxed break-words">{{ formattedDeliveryAddress }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="h-px bg-concrete-200 my-6"></div>

                            <!-- Items List - Collapsible -->
                            <Collapsible v-model:open="isItemsOpen">
                                <CollapsibleTrigger as-child>
                                    <button type="button" class="flex items-center justify-between w-full px-4 py-3 bg-gradient-to-br from-steel-100/30 to-steel-50/20 border border-steel-200/50 rounded-xl cursor-pointer transition-all duration-200 mb-4 hover:bg-gradient-to-br hover:from-steel-100/50 hover:to-steel-50/30 hover:border-steel-700 hover:-translate-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <Package :size="18" class="text-steel-700 flex-shrink-0" />
                                            <span class="font-body text-base font-semibold text-steel-900">
                                                {{ selectedCart.items_count }}
                                                {{ t({ ru: selectedCart.items_count === 1 ? 'Товар' : 'Товаров', kz: 'Тауар' }) }}
                                            </span>
                                        </div>
                                        <ChevronDown
                                            :size="20"
                                            class="text-concrete-600 transition-transform duration-200 flex-shrink-0"
                                            :class="{ 'rotate-180': isItemsOpen }"
                                        />
                                    </button>
                                </CollapsibleTrigger>

                                <CollapsibleContent>
                                    <div class="flex flex-col gap-4 pt-2">
                                        <div
                                            v-for="item in selectedCart.items"
                                            :key="item.id"
                                            class="flex items-start justify-between gap-4"
                                        >
                                            <div class="flex-1 min-w-0">
                                                <div class="font-body font-semibold text-steel-900 text-base mb-1">{{ getProductName(item) }}</div>
                                                <div class="font-body text-sm text-concrete-600">{{ t({ ru: 'Кол-во:', kz: 'Саны:' }) }} {{ item.quantity }}</div>
                                            </div>
                                            <PriceDisplay
                                                :price="getItemSubtotal(item)"
                                                class="font-body font-semibold text-steel-900 flex-shrink-0"
                                            />
                                        </div>
                                    </div>
                                </CollapsibleContent>
                            </Collapsible>

                            <div class="h-px bg-concrete-200 my-6"></div>

                            <!-- Totals -->
                            <div class="flex flex-col gap-3">
                                <div class="flex items-center justify-between text-base">
                                    <span class="font-body text-concrete-600 font-medium">{{ t({ ru: 'Промежуточный итог', kz: 'Аралық қорытынды' }) }}</span>
                                    <PriceDisplay :price="selectedCart.total" class="font-body font-semibold text-steel-900" />
                                </div>
                                <div class="flex items-center justify-between text-base">
                                    <span class="font-body text-concrete-600 font-medium">{{ t({ ru: 'Доставка', kz: 'Жеткізу' }) }}</span>
                                    <span class="font-body text-sm text-concrete-500 italic">{{ t({ ru: 'Рассчитывается после заказа', kz: 'Тапсырыстан кейін есептеледі' }) }}</span>
                                </div>
                            </div>

                            <div class="h-0.5 bg-gradient-to-r from-steel-600 to-steel-700 my-6 rounded-full"></div>

                            <div class="flex items-center justify-between">
                                <span class="font-display text-xl font-bold text-steel-900">{{ t({ ru: 'Итого', kz: 'Барлығы' }) }}</span>
                                <PriceDisplay
                                    :price="selectedCart.total"
                                    class="font-display text-2xl font-bold text-steel-900"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Form -->
                    <div class="order-2 lg:order-1">
                        <!-- Shop Selection (if multiple shops) -->
                        <div v-if="carts.length > 1" class="bg-white rounded-2xl p-8 mb-8 shadow-industrial-md border border-concrete-200">
                            <h3 class="font-display text-xl font-bold text-steel-900 mb-2">{{ t({ ru: 'Выберите магазин', kz: 'Дүкенді таңдаңыз' }) }}</h3>
                            <p class="font-body text-base text-concrete-600 mb-6">{{ t({ ru: 'Выберите магазин для заказа', kz: 'Тапсырыс үшін дүкенді таңдаңыз' }) }}</p>

                            <div class="flex flex-col gap-3">
                                <button
                                    v-for="cart in carts"
                                    :key="cart.id"
                                    @click="selectedShopId = cart.shop_id"
                                    type="button"
                                    class="flex items-center justify-between p-5 bg-concrete-50 border-2 border-concrete-300 rounded-xl cursor-pointer transition-all duration-200 hover:border-steel-700 hover:bg-white hover:-translate-y-0.5 hover:shadow-industrial-md"
                                    :class="{ 'border-steel-700 bg-gradient-to-br from-steel-100/30 to-steel-50/20 shadow-[0_0_0_3px_rgba(45,58,58,0.1)]': selectedShopId === cart.shop_id }"
                                >
                                    <div class="flex items-center gap-4">
                                        <Store :size="20" class="text-concrete-500 transition-colors" :class="{ 'text-steel-700': selectedShopId === cart.shop_id }" />
                                        <div class="text-left">
                                            <div class="font-display font-semibold text-steel-900 mb-1">{{ cart.shop?.name }}</div>
                                            <div class="font-body text-sm text-concrete-600">
                                                {{ cart.items_count }}
                                                {{ t({ ru: cart.items_count === 1 ? 'товар' : 'товаров', kz: 'тауар' }) }}
                                            </div>
                                        </div>
                                    </div>
                                    <PriceDisplay :price="cart.total" class="font-display font-bold text-steel-900" />
                                </button>
                            </div>
                        </div>

                        <!-- Delivery Form -->
                        <Form
                            v-if="selectedCart"
                            action="/checkout"
                            method="post"
                            #default="{ processing, errors }"
                        >
                            <input type="hidden" name="shop_id" :value="selectedShopId" />

                            <div class="bg-white rounded-2xl p-8 shadow-industrial-md border border-concrete-200 mb-6">
                                <div class="flex items-center gap-3 mb-8 pb-6 border-b-2 border-concrete-200">
                                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-steel-600 to-steel-700 rounded-xl text-white shadow-industrial-sm">
                                        <MapPin :size="24" :stroke-width="2.5" />
                                    </div>
                                    <h3 class="font-display text-2xl font-bold text-steel-900">{{ t({ ru: 'Информация о доставке', kz: 'Жеткізу туралы ақпарат' }) }}</h3>
                                </div>

                                <div class="flex flex-col gap-6">
                                    <!-- Address -->
                                    <div class="flex flex-col gap-2">
                                        <Label for="delivery_address" class="font-display text-base font-semibold text-steel-900">
                                            {{ t({ ru: 'Адрес *', kz: 'Мекенжайы *' }) }}
                                        </Label>
                                        <Input
                                            id="delivery_address"
                                            name="delivery_address"
                                            type="text"
                                            v-model="deliveryAddress"
                                            :placeholder="t({ ru: 'Название улицы, номер дома', kz: 'Көше атауы, үй нөмірі' })"
                                            class="font-body h-12 px-4 border-2 border-concrete-300 rounded-lg text-base text-steel-900 bg-white transition-all duration-200 hover:border-steel-700 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20"
                                        />
                                        <p v-if="errors.delivery_address" class="font-body text-sm text-rust-600 font-medium">
                                            {{ errors.delivery_address }}
                                        </p>
                                    </div>

                                    <!-- Apartment Details Grid -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2">
                                            <Label for="delivery_entry" class="font-display text-base font-semibold text-steel-900">{{ t({ ru: 'Подъезд', kz: 'Кіреберіс' }) }}</Label>
                                            <Input
                                                id="delivery_entry"
                                                name="delivery_entry"
                                                type="text"
                                                v-model="deliveryEntry"
                                                :placeholder="t({ ru: 'напр., 2', kz: 'мыс., 2' })"
                                                class="font-body h-12 px-4 border-2 border-concrete-300 rounded-lg text-base text-steel-900 bg-white transition-all duration-200 hover:border-steel-700 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20"
                                            />
                                            <p v-if="errors.delivery_entry" class="font-body text-sm text-rust-600 font-medium">
                                                {{ errors.delivery_entry }}
                                            </p>
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <Label for="delivery_floor" class="font-display text-base font-semibold text-steel-900">{{ t({ ru: 'Этаж', kz: 'Қабат' }) }}</Label>
                                            <Input
                                                id="delivery_floor"
                                                name="delivery_floor"
                                                type="text"
                                                v-model="deliveryFloor"
                                                :placeholder="t({ ru: 'напр., 5', kz: 'мыс., 5' })"
                                                class="font-body h-12 px-4 border-2 border-concrete-300 rounded-lg text-base text-steel-900 bg-white transition-all duration-200 hover:border-steel-700 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20"
                                            />
                                            <p v-if="errors.delivery_floor" class="font-body text-sm text-rust-600 font-medium">
                                                {{ errors.delivery_floor }}
                                            </p>
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <Label for="delivery_apartment" class="font-display text-base font-semibold text-steel-900">
                                                {{ t({ ru: 'Квартира *', kz: 'Пәтер *' }) }}
                                            </Label>
                                            <Input
                                                id="delivery_apartment"
                                                name="delivery_apartment"
                                                type="text"
                                                v-model="deliveryApartment"
                                                :placeholder="t({ ru: 'напр., 23', kz: 'мыс., 23' })"
                                                class="font-body h-12 px-4 border-2 border-concrete-300 rounded-lg text-base text-steel-900 bg-white transition-all duration-200 hover:border-steel-700 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20"
                                            />
                                            <p v-if="errors.delivery_apartment" class="font-body text-sm text-rust-600 font-medium">
                                                {{ errors.delivery_apartment }}
                                            </p>
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <Label for="delivery_intercom" class="font-display text-base font-semibold text-steel-900">
                                                {{ t({ ru: 'Домофон', kz: 'Домофон' }) }}
                                            </Label>
                                            <Input
                                                id="delivery_intercom"
                                                name="delivery_intercom"
                                                type="text"
                                                v-model="deliveryIntercom"
                                                :placeholder="t({ ru: 'напр., 123', kz: 'мыс., 123' })"
                                                class="font-body h-12 px-4 border-2 border-concrete-300 rounded-lg text-base text-steel-900 bg-white transition-all duration-200 hover:border-steel-700 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20"
                                            />
                                            <p v-if="errors.delivery_intercom" class="font-body text-sm text-rust-600 font-medium">
                                                {{ errors.delivery_intercom }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Delivery Notes -->
                                    <div class="flex flex-col gap-2">
                                        <Label for="delivery_notes" class="font-display text-base font-semibold text-steel-900">
                                            {{ t({ ru: 'Примечания к доставке (необязательно)', kz: 'Жеткізу туралы ескертпелер (міндетті емес)' }) }}
                                        </Label>
                                        <textarea
                                            id="delivery_notes"
                                            name="delivery_notes"
                                            rows="3"
                                            :placeholder="t({ ru: 'Любые особые инструкции для доставки...', kz: 'Жеткізу үшін арнайы нұсқаулар...' })"
                                            class="font-body min-h-[100px] px-4 py-3 border-2 border-concrete-300 rounded-lg text-base text-steel-900 bg-white transition-all duration-200 resize-vertical hover:border-steel-700 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20"
                                        ></textarea>
                                        <p v-if="errors.delivery_notes" class="font-body text-sm text-rust-600 font-medium">
                                            {{ errors.delivery_notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex gap-4 flex-col sm:flex-row-reverse">
                                <Button
                                    type="submit"
                                    size="lg"
                                    class="font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl hover:bg-amber-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex-1 inline-flex items-center justify-center gap-2"
                                    :disabled="processing"
                                >
                                    <ShoppingBag :size="20" />
                                    {{ processing ? t({ ru: 'Обработка...', kz: 'Өңдеу...' }) : t({ ru: 'Оформить заказ', kz: 'Тапсырысты рәсімдеу' }) }}
                                </Button>
                                <Button
                                    variant="outline"
                                    size="lg"
                                    as-child
                                    class="font-display font-bold px-6 py-4 bg-transparent text-steel-700 border-2 border-steel-700 rounded-xl hover:bg-steel-700 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="processing"
                                >
                                    <Link href="/cart">{{ t({ ru: 'Отмена', kz: 'Болдырмау' }) }}</Link>
                                </Button>
                            </div>
                        </Form>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<style scoped>
/* Industrial Refined Design System for Checkout */
.checkout-page {
    background: #f8f7f5;
    min-height: 100vh;
    position: relative;
}

/* Subtle background pattern */
.checkout-page::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image:
        linear-gradient(90deg, rgba(45, 58, 58, 0.03) 1px, transparent 1px),
        linear-gradient(rgba(45, 58, 58, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    z-index: 0;
    opacity: 0.5;
}

.checkout-page > * {
    position: relative;
    z-index: 1;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Shop Info */
.shop-info {
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.08) 0%,
        rgba(44, 95, 93, 0.04) 100%);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(44, 95, 93, 0.1);
}

.shop-details {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.shop-icon {
    color: var(--color-primary);
    flex-shrink: 0;
    margin-top: 2px;
}

.shop-text {
    flex: 1;
    min-width: 0;
}

.shop-name {
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 0.25rem;
}

.shop-items {
    font-size: 0.875rem;
    color: var(--color-text-secondary);
    font-weight: 500;
}

.shop-location {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(44, 95, 93, 0.15);
}

.location-icon {
    color: var(--color-text-secondary);
    flex-shrink: 0;
    margin-top: 2px;
}

.location-text {
    flex: 1;
    min-width: 0;
}

.location-city {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 0.125rem;
}

.location-address {
    font-size: 0.875rem;
    color: var(--color-text-secondary);
}

/* Delivery Preview */
.delivery-preview {
    background: linear-gradient(135deg,
        rgba(217, 119, 87, 0.1) 0%,
        rgba(217, 119, 87, 0.05) 100%);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(217, 119, 87, 0.2);
    animation: slideIn var(--transition-base);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.delivery-preview-content {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.delivery-icon {
    color: var(--color-accent);
    flex-shrink: 0;
    margin-top: 2px;
}

.delivery-label {
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 0.25rem;
}

.delivery-text {
    font-size: 0.875rem;
    color: var(--color-text-secondary);
    line-height: 1.5;
    word-break: break-word;
}

/* Summary Dividers */
.summary-divider {
    height: 1px;
    background: var(--color-border);
    margin: 1.5rem 0;
}

.summary-divider-bold {
    height: 2px;
    background: linear-gradient(90deg,
        var(--color-primary) 0%,
        var(--color-primary-light) 100%);
    margin: 1.5rem 0;
}

/* Items Toggle Button */
.items-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.06) 0%,
        rgba(44, 95, 93, 0.03) 100%);
    border: 1px solid rgba(44, 95, 93, 0.15);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-base);
    font-family: var(--font-body);
    margin-bottom: 1rem;
}

.items-toggle:hover {
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.1) 0%,
        rgba(44, 95, 93, 0.05) 100%);
    border-color: var(--color-primary-light);
    transform: translateY(-1px);
}

.items-toggle-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.items-toggle-icon {
    color: var(--color-primary);
    flex-shrink: 0;
}

.items-toggle-text {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--color-text-primary);
}

.items-toggle-chevron {
    color: var(--color-text-secondary);
    transition: transform var(--transition-base);
    flex-shrink: 0;
}

.items-toggle-chevron.rotate-180 {
    transform: rotate(180deg);
}

/* Items List */
.items-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-top: 0.5rem;
}

.item-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.item-details {
    flex: 1;
    min-width: 0;
}

.item-name {
    font-weight: 600;
    color: var(--color-text-primary);
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
}

.item-quantity {
    font-size: 0.875rem;
    color: var(--color-text-secondary);
}

.item-price {
    font-weight: 600;
    color: var(--color-text-primary);
    flex-shrink: 0;
}

/* Totals */
.totals-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.total-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.9375rem;
}

.total-label {
    color: var(--color-text-secondary);
    font-weight: 500;
}

.total-value {
    font-weight: 600;
    color: var(--color-text-primary);
}

.shipping-note {
    font-size: 0.875rem;
    color: var(--color-text-muted);
    font-style: italic;
}

.grand-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.grand-total-label {
    font-family: var(--font-display);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-text-primary);
}

.grand-total-value {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-primary);
}

/* Shop Selection */
.shop-selection {
    background: var(--color-surface);
    border-radius: var(--radius-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--color-border);
}

.section-title {
    font-family: var(--font-display);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.5rem;
}

.section-subtitle {
    font-size: 0.9375rem;
    color: var(--color-text-secondary);
    margin-bottom: 1.5rem;
}

.shop-options {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.shop-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem;
    background: var(--color-bg);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-base);
    font-family: var(--font-body);
}

.shop-option:hover {
    border-color: var(--color-primary-light);
    background: var(--color-surface);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.shop-option.active {
    border-color: var(--color-primary);
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.05) 0%,
        rgba(44, 95, 93, 0.02) 100%);
    box-shadow: 0 0 0 3px rgba(44, 95, 93, 0.1);
}

.shop-option-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.shop-option-icon {
    color: var(--color-text-muted);
    transition: color var(--transition-base);
}

.shop-option.active .shop-option-icon {
    color: var(--color-primary);
}

.shop-option-text {
    text-align: left;
}

.shop-option-name {
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 0.25rem;
}

.shop-option-count {
    font-size: 0.875rem;
    color: var(--color-text-secondary);
}

.shop-option-price {
    font-weight: 700;
    color: var(--color-text-primary);
}

/* Delivery Form */
.delivery-form {
    background: var(--color-surface);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--color-border);
    margin-bottom: 1.5rem;
}

.form-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--color-border);
}

.form-icon {
    color: var(--color-primary);
}

.form-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-text-primary);
}

.form-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--color-text-primary);
}

.form-input {
    height: 48px;
    padding: 0 1rem;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--color-text-primary);
    background: var(--color-surface);
    transition: all var(--transition-base);
}

.form-input:hover {
    border-color: var(--color-primary-light);
}

.form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(44, 95, 93, 0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 640px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-textarea {
    min-height: 100px;
    padding: 0.75rem 1rem;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--color-text-primary);
    background: var(--color-surface);
    transition: all var(--transition-base);
    resize: vertical;
}

.form-textarea:hover {
    border-color: var(--color-primary-light);
}

.form-textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(44, 95, 93, 0.1);
}

.form-error {
    font-size: 0.875rem;
    color: #DC2626;
    font-weight: 500;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
}

.submit-button {
    flex: 1;
    height: 56px;
    background: linear-gradient(135deg,
        var(--color-accent) 0%,
        var(--color-accent-light) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 1.0625rem;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition-base);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 4px 14px rgba(217, 119, 87, 0.3);
}

.submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 87, 0.4);
}

.submit-button:active:not(:disabled) {
    transform: translateY(0);
}

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.cancel-button {
    height: 56px;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--color-text-secondary);
    background: var(--color-surface);
    cursor: pointer;
    transition: all var(--transition-base);
    padding: 0 2rem;
}

.cancel-button:hover:not(:disabled) {
    border-color: var(--color-text-primary);
    color: var(--color-text-primary);
    background: var(--color-bg);
}

@media (max-width: 640px) {
    .form-actions {
        flex-direction: column-reverse;
    }

    .submit-button,
    .cancel-button {
        width: 100%;
    }
}
</style>
