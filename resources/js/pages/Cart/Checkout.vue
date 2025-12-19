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
                <div class="page-header">
                    <h1 class="page-title">{{ t({ ru: 'Безопасное оформление', kz: 'Қауіпсіз рәсімдеу' }) }}</h1>
                    <p class="page-subtitle">
                        {{ t({ ru: 'Заполните информацию о доставке для завершения заказа', kz: 'Тапсырысты аяқтау үшін жеткізу туралы ақпаратты толтырыңыз' }) }}
                    </p>
                </div>

                <div class="checkout-grid">
                    <!-- Order Summary (appears first on mobile) -->
                    <div class="summary-column">
                        <div class="summary-card" v-if="selectedCart">
                            <div class="summary-header">
                                <Package :size="24" class="summary-icon" />
                                <h2 class="summary-title">{{ t({ ru: 'Сводка заказа', kz: 'Тапсырыс қорытындысы' }) }}</h2>
                            </div>

                            <!-- Shop Info -->
                            <div class="shop-info">
                                <div class="shop-details">
                                    <Store :size="20" class="shop-icon" />
                                    <div class="shop-text">
                                        <div class="shop-name">{{ selectedCart.shop?.name }}</div>
                                        <div class="shop-items">
                                            {{ selectedCart.items_count }}
                                            {{ t({ ru: selectedCart.items_count === 1 ? 'товар' : 'товаров', kz: 'тауар' }) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Shop Location -->
                                <div v-if="selectedCart.shop?.city || selectedCart.shop?.address" class="shop-location">
                                    <MapPin :size="16" class="location-icon" />
                                    <div class="location-text">
                                        <div v-if="selectedCart.shop?.city" class="location-city">
                                            {{ selectedCart.shop.city.name }}
                                        </div>
                                        <div v-if="selectedCart.shop?.address" class="location-address">
                                            {{ selectedCart.shop.address }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Address Preview -->
                            <div v-if="formattedDeliveryAddress" class="delivery-preview">
                                <div class="delivery-preview-content">
                                    <MapPin :size="20" class="delivery-icon" />
                                    <div>
                                        <div class="delivery-label">{{ t({ ru: 'Адрес доставки', kz: 'Жеткізу мекенжайы' }) }}</div>
                                        <div class="delivery-text">{{ formattedDeliveryAddress }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="summary-divider"></div>

                            <!-- Items List - Collapsible -->
                            <Collapsible v-model:open="isItemsOpen">
                                <CollapsibleTrigger as-child>
                                    <button type="button" class="items-toggle">
                                        <div class="items-toggle-content">
                                            <Package :size="18" class="items-toggle-icon" />
                                            <span class="items-toggle-text">
                                                {{ selectedCart.items_count }}
                                                {{ t({ ru: selectedCart.items_count === 1 ? 'Товар' : 'Товаров', kz: 'Тауар' }) }}
                                            </span>
                                        </div>
                                        <ChevronDown
                                            :size="20"
                                            class="items-toggle-chevron"
                                            :class="{ 'rotate-180': isItemsOpen }"
                                        />
                                    </button>
                                </CollapsibleTrigger>

                                <CollapsibleContent>
                                    <div class="items-list">
                                        <div
                                            v-for="item in selectedCart.items"
                                            :key="item.id"
                                            class="item-row"
                                        >
                                            <div class="item-details">
                                                <div class="item-name">{{ getProductName(item) }}</div>
                                                <div class="item-quantity">{{ t({ ru: 'Кол-во:', kz: 'Саны:' }) }} {{ item.quantity }}</div>
                                            </div>
                                            <PriceDisplay
                                                :price="getItemSubtotal(item)"
                                                class="item-price"
                                            />
                                        </div>
                                    </div>
                                </CollapsibleContent>
                            </Collapsible>

                            <div class="summary-divider"></div>

                            <!-- Totals -->
                            <div class="totals-section">
                                <div class="total-row">
                                    <span class="total-label">{{ t({ ru: 'Промежуточный итог', kz: 'Аралық қорытынды' }) }}</span>
                                    <PriceDisplay :price="selectedCart.total" class="total-value" />
                                </div>
                                <div class="total-row">
                                    <span class="total-label">{{ t({ ru: 'Доставка', kz: 'Жеткізу' }) }}</span>
                                    <span class="shipping-note">{{ t({ ru: 'Рассчитывается после заказа', kz: 'Тапсырыстан кейін есептеледі' }) }}</span>
                                </div>
                            </div>

                            <div class="summary-divider-bold"></div>

                            <div class="grand-total">
                                <span class="grand-total-label">{{ t({ ru: 'Итого', kz: 'Барлығы' }) }}</span>
                                <PriceDisplay
                                    :price="selectedCart.total"
                                    class="grand-total-value"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Form -->
                    <div class="form-column">
                        <!-- Shop Selection (if multiple shops) -->
                        <div v-if="carts.length > 1" class="shop-selection">
                            <h3 class="section-title">{{ t({ ru: 'Выберите магазин', kz: 'Дүкенді таңдаңыз' }) }}</h3>
                            <p class="section-subtitle">{{ t({ ru: 'Выберите магазин для заказа', kz: 'Тапсырыс үшін дүкенді таңдаңыз' }) }}</p>

                            <div class="shop-options">
                                <button
                                    v-for="cart in carts"
                                    :key="cart.id"
                                    @click="selectedShopId = cart.shop_id"
                                    type="button"
                                    class="shop-option"
                                    :class="{ active: selectedShopId === cart.shop_id }"
                                >
                                    <div class="shop-option-content">
                                        <Store :size="20" class="shop-option-icon" />
                                        <div class="shop-option-text">
                                            <div class="shop-option-name">{{ cart.shop?.name }}</div>
                                            <div class="shop-option-count">
                                                {{ cart.items_count }}
                                                {{ t({ ru: cart.items_count === 1 ? 'товар' : 'товаров', kz: 'тауар' }) }}
                                            </div>
                                        </div>
                                    </div>
                                    <PriceDisplay :price="cart.total" class="shop-option-price" />
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

                            <div class="delivery-form">
                                <div class="form-header">
                                    <MapPin :size="24" class="form-icon" />
                                    <h3 class="form-title">{{ t({ ru: 'Информация о доставке', kz: 'Жеткізу туралы ақпарат' }) }}</h3>
                                </div>

                                <div class="form-content">
                                    <!-- Address -->
                                    <div class="form-group">
                                        <Label for="delivery_address" class="form-label">
                                            {{ t({ ru: 'Адрес улицы *', kz: 'Көше мекенжайы *' }) }}
                                        </Label>
                                        <Input
                                            id="delivery_address"
                                            name="delivery_address"
                                            type="text"
                                            required
                                            v-model="deliveryAddress"
                                            :placeholder="t({ ru: 'Название улицы, номер дома', kz: 'Көше атауы, үй нөмірі' })"
                                            class="form-input"
                                        />
                                        <p v-if="errors.delivery_address" class="form-error">
                                            {{ errors.delivery_address }}
                                        </p>
                                    </div>

                                    <!-- Apartment Details Grid -->
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <Label for="delivery_entry" class="form-label">{{ t({ ru: 'Подъезд', kz: 'Кіреберіс' }) }}</Label>
                                            <Input
                                                id="delivery_entry"
                                                name="delivery_entry"
                                                type="text"
                                                v-model="deliveryEntry"
                                                :placeholder="t({ ru: 'напр., 2', kz: 'мыс., 2' })"
                                                class="form-input"
                                            />
                                            <p v-if="errors.delivery_entry" class="form-error">
                                                {{ errors.delivery_entry }}
                                            </p>
                                        </div>

                                        <div class="form-group">
                                            <Label for="delivery_floor" class="form-label">{{ t({ ru: 'Этаж', kz: 'Қабат' }) }}</Label>
                                            <Input
                                                id="delivery_floor"
                                                name="delivery_floor"
                                                type="text"
                                                v-model="deliveryFloor"
                                                :placeholder="t({ ru: 'напр., 5', kz: 'мыс., 5' })"
                                                class="form-input"
                                            />
                                            <p v-if="errors.delivery_floor" class="form-error">
                                                {{ errors.delivery_floor }}
                                            </p>
                                        </div>

                                        <div class="form-group">
                                            <Label for="delivery_apartment" class="form-label">
                                                {{ t({ ru: 'Квартира', kz: 'Пәтер' }) }}
                                            </Label>
                                            <Input
                                                id="delivery_apartment"
                                                name="delivery_apartment"
                                                type="text"
                                                v-model="deliveryApartment"
                                                :placeholder="t({ ru: 'напр., 23', kz: 'мыс., 23' })"
                                                class="form-input"
                                            />
                                            <p v-if="errors.delivery_apartment" class="form-error">
                                                {{ errors.delivery_apartment }}
                                            </p>
                                        </div>

                                        <div class="form-group">
                                            <Label for="delivery_intercom" class="form-label">
                                                {{ t({ ru: 'Домофон', kz: 'Домофон' }) }}
                                            </Label>
                                            <Input
                                                id="delivery_intercom"
                                                name="delivery_intercom"
                                                type="text"
                                                v-model="deliveryIntercom"
                                                :placeholder="t({ ru: 'напр., 123', kz: 'мыс., 123' })"
                                                class="form-input"
                                            />
                                            <p v-if="errors.delivery_intercom" class="form-error">
                                                {{ errors.delivery_intercom }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Delivery Notes -->
                                    <div class="form-group">
                                        <Label for="delivery_notes" class="form-label">
                                            {{ t({ ru: 'Примечания к доставке (необязательно)', kz: 'Жеткізу туралы ескертпелер (міндетті емес)' }) }}
                                        </Label>
                                        <textarea
                                            id="delivery_notes"
                                            name="delivery_notes"
                                            rows="3"
                                            :placeholder="t({ ru: 'Любые особые инструкции для доставки...', kz: 'Жеткізу үшін арнайы нұсқаулар...' })"
                                            class="form-textarea"
                                        ></textarea>
                                        <p v-if="errors.delivery_notes" class="form-error">
                                            {{ errors.delivery_notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-actions">
                                <Button
                                    type="submit"
                                    size="lg"
                                    class="submit-button py-4"
                                    :disabled="processing"
                                >
                                    <ShoppingBag :size="20" />
                                    {{ processing ? t({ ru: 'Обработка...', kz: 'Өңдеу...' }) : t({ ru: 'Оформить заказ', kz: 'Тапсырысты рәсімдеу' }) }}
                                </Button>
                                <Button
                                    variant="outline"
                                    size="lg"
                                    as-child
                                    class="cancel-button"
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
/* Import distinctive fonts */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap');

/* CSS Variables for cohesive theming */
.checkout-page {
    --color-primary: #2C5F5D;
    --color-primary-light: #3D7A77;
    --color-primary-dark: #1F4544;
    --color-accent: #D97757;
    --color-accent-light: #E89375;
    --color-bg: #FAF9F6;
    --color-surface: #FFFFFF;
    --color-surface-elevated: #FFFFFF;
    --color-text-primary: #1A1A1A;
    --color-text-secondary: #6B6B6B;
    --color-text-muted: #9E9E9E;
    --color-border: #E5E5E5;
    --color-border-focus: var(--color-primary);
    --color-success: #4A9B7F;
    --color-warning: #F59E0B;

    --font-display: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-body: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;

    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.1);

    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;

    --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);

    background: var(--color-bg);
    min-height: 100vh;
    font-family: var(--font-body);
    color: var(--color-text-primary);
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
        linear-gradient(90deg, rgba(44, 95, 93, 0.02) 1px, transparent 1px),
        linear-gradient(rgba(44, 95, 93, 0.02) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    z-index: 0;
}

.checkout-page > * {
    position: relative;
    z-index: 1;
}

/* Page Header */
.page-header {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInUp var(--transition-slow);
}

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

.page-title {
    font-family: var(--font-display);
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.page-subtitle {
    font-size: 1.125rem;
    color: var(--color-text-secondary);
    font-weight: 500;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }

    .page-subtitle {
        font-size: 1rem;
    }
}

/* Checkout Grid */
.checkout-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 1fr 400px;
        gap: 3rem;
    }
}

.form-column {
    order: 2;
    animation: fadeInUp var(--transition-slow);
    animation-delay: 100ms;
    animation-fill-mode: both;
}

.summary-column {
    order: 1;
    animation: fadeInUp var(--transition-slow);
    animation-delay: 200ms;
    animation-fill-mode: both;
}

@media (min-width: 1024px) {
    .form-column {
        order: 1;
    }

    .summary-column {
        order: 2;
    }
}

/* Summary Card */
.summary-card {
    background: var(--color-surface-elevated);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(44, 95, 93, 0.1);
    position: sticky;
    top: 1rem;
    backdrop-filter: blur(10px);
    background: linear-gradient(135deg,
        rgba(255, 255, 255, 0.95) 0%,
        rgba(250, 249, 246, 0.95) 100%);
}

.summary-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.summary-icon {
    color: var(--color-primary);
}

.summary-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-text-primary);
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
