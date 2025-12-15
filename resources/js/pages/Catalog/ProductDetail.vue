<script setup lang="ts">
import ProductCard from '@/components/shop/ProductCard.vue';
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Input } from '@/components/ui/input';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { useCartStore } from '@/stores/cart';
import { useLocaleStore } from '@/stores/locale';
import type { Product } from '@/types/api';
import { Head } from '@inertiajs/vue3';
import { Check, Minus, Package, Plus, ShoppingCart, Store } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    product: Product;
    similarProducts: Product[];
}

const props = defineProps<Props>();
const { getLocalizedName, getLocalizedDescription, t } = useLocale();
const cartStore = useCartStore();
const localeStore = useLocaleStore();

const quantity = ref(1);
const selectedImageIndex = ref(0);
const adding = ref(false);
const showSuccess = ref(false);

const productName = computed(() => getLocalizedName(props.product));
const productImages = computed(() => props.product.images || []);
const selectedImage = computed(() => productImages.value[selectedImageIndex.value] || null);
const isOutOfStock = computed(() => props.product.quantity === 0);
const maxQuantity = computed(() => Math.min(props.product.quantity, 999));

const categoryName = computed(() =>
    props.product.nomenclature?.category
        ? getLocalizedName(props.product.nomenclature.category)
        : ''
);
const nomenclatureName = computed(() =>
    props.product.nomenclature ? getLocalizedName(props.product.nomenclature) : ''
);
const nomenclatureDescription = computed(() =>
    props.product.nomenclature ? getLocalizedDescription(props.product.nomenclature) : null
);
const unitName = computed(() =>
    props.product.nomenclature?.unit ? getLocalizedName(props.product.nomenclature.unit) : ''
);

function incrementQuantity() {
    if (quantity.value < maxQuantity.value) {
        quantity.value++;
    }
}

function decrementQuantity() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

function selectImage(index: number) {
    selectedImageIndex.value = index;
}

async function handleAddToCart() {
    if (isOutOfStock.value || adding.value) return;

    adding.value = true;
    showSuccess.value = false;

    try {
        await cartStore.addItem({
            product_id: props.product.id,
            quantity: quantity.value,
        });

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
    <Head :title="productName" />

    <ShopLayout>
        <div class="product-detail-page bg-pattern">
            <div class="page-container">
                <!-- Breadcrumbs -->
                <Breadcrumb class="breadcrumb-nav animate-fadeIn">
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink href="/" class="breadcrumb-link">
                                {{ t({ ru: 'Главная', kz: 'Басты бет' }) }}
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbLink href="/products" class="breadcrumb-link">
                                {{ t({ ru: 'Товары', kz: 'Тауарлар' }) }}
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="product.nomenclature?.category" />
                        <BreadcrumbItem v-if="product.nomenclature?.category">
                            <BreadcrumbLink
                                :href="`/categories/${product.nomenclature.category.slug}`"
                                class="breadcrumb-link"
                            >
                                {{ categoryName }}
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbPage class="breadcrumb-current">{{ productName }}</BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>

                <!-- Product Detail -->
                <div class="product-grid">
                    <!-- Image Gallery -->
                    <div class="gallery-section animate-fadeInUp">
                        <!-- Main Image -->
                        <div class="main-image">
                            <img
                                v-if="selectedImage"
                                :src="selectedImage"
                                :alt="productName"
                                class="product-image"
                            />
                            <div v-else class="image-placeholder">
                                <Package :size="64" />
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        <div v-if="productImages.length > 1" class="thumbnails-grid">
                            <button
                                v-for="(image, index) in productImages"
                                :key="index"
                                @click="selectImage(index)"
                                :class="['thumbnail', { active: selectedImageIndex === index }]"
                            >
                                <img :src="image" :alt="`${productName} - ${index + 1}`" />
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="info-section">
                        <!-- Title and Price -->
                        <div class="product-header animate-fadeInUp" style="animation-delay: 100ms">
                            <h1 class="product-title">{{ productName }}</h1>
                            <p v-if="nomenclatureName" class="product-subtitle">
                                {{ nomenclatureName }}
                            </p>
                            <div class="price-section">
                                <PriceDisplay :price="product.price" class="product-price" />
                                <span class="price-unit">/ {{ unitName }}</span>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <!-- Description -->
                        <div
                            v-if="nomenclatureDescription"
                            class="description-section animate-fadeInUp"
                            style="animation-delay: 150ms"
                        >
                            <h3 class="section-heading">
                                {{ t({ ru: 'Описание', kz: 'Сипаттама' }) }}
                            </h3>
                            <p class="description-text">{{ nomenclatureDescription }}</p>
                        </div>

                        <!-- Availability -->
                        <div class="availability-section animate-fadeInUp" style="animation-delay: 200ms">
                            <div :class="['availability-badge', { 'out-of-stock': isOutOfStock }]">
                                <span :class="['status-dot', { 'unavailable': isOutOfStock }]" />
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

                        <div class="divider"></div>

                        <!-- Quantity Selector and Add to Cart -->
                        <div class="purchase-section animate-fadeInUp" style="animation-delay: 250ms">
                            <div class="quantity-section">
                                <label class="quantity-label">
                                    {{ t({ ru: 'Количество', kz: 'Саны' }) }}
                                </label>
                                <div class="quantity-controls">
                                    <button
                                        @click="decrementQuantity"
                                        :disabled="quantity <= 1 || isOutOfStock"
                                        class="qty-btn"
                                    >
                                        <Minus :size="16" />
                                    </button>
                                    <Input
                                        v-model.number="quantity"
                                        type="number"
                                        :min="1"
                                        :max="maxQuantity"
                                        :disabled="isOutOfStock"
                                        class="qty-input"
                                    />
                                    <button
                                        @click="incrementQuantity"
                                        :disabled="quantity >= maxQuantity || isOutOfStock"
                                        class="qty-btn"
                                    >
                                        <Plus :size="16" />
                                    </button>
                                </div>
                            </div>

                            <button
                                @click="handleAddToCart"
                                :disabled="isOutOfStock || adding"
                                :class="['add-to-cart-btn', { success: showSuccess }]"
                            >
                                <Check v-if="showSuccess" :size="20" />
                                <ShoppingCart v-else :size="20" />
                                <span v-if="showSuccess">
                                    {{ t({ ru: 'Добавлено!', kz: 'Қосылды!' }) }}
                                </span>
                                <span v-else-if="adding">
                                    {{ t({ ru: 'Добавление...', kz: 'Қосылуда...' }) }}
                                </span>
                                <span v-else-if="isOutOfStock">
                                    {{ t({ ru: 'Нет в наличии', kz: 'Қолжетімсіз' }) }}
                                </span>
                                <span v-else>
                                    {{ t({ ru: 'Добавить в корзину', kz: 'Себетке қосу' }) }}
                                </span>
                            </button>
                        </div>

                        <div class="divider"></div>

                        <!-- Shop Info -->
                        <div
                            v-if="product.shop"
                            class="shop-info-card animate-fadeInUp"
                            style="animation-delay: 300ms"
                        >
                            <div class="shop-header">
                                <Store :size="20" class="shop-icon" />
                                <h3 class="shop-title">
                                    {{ t({ ru: 'Продавец', kz: 'Сатушы' }) }}
                                </h3>
                            </div>
                            <div class="shop-content">
                                <p class="shop-name">{{ product.shop.name }}</p>
                                <p v-if="product.shop.description" class="shop-description">
                                    {{ product.shop.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Specs -->
                <div v-if="product.nomenclature?.specs && product.nomenclature.specs.length > 0" class="specs-section">
                    <h2 class="specs-title animate-fadeInUp">
                        {{ t({ ru: 'Характеристики', kz: 'Сипаттамалар' }) }}
                    </h2>
                    <div class="specs-card animate-fadeInUp" style="animation-delay: 100ms">
                        <div
                            v-for="(spec, index) in product.nomenclature.specs"
                            :key="index"
                            class="spec-row"
                        >
                            <div class="spec-key">
                                {{ localeStore.currentLocale === 'ru' ? spec.key_ru : spec.key_kz }}
                            </div>
                            <div class="spec-value">
                                {{ localeStore.currentLocale === 'ru' ? spec.value_ru : spec.value_kz }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Products -->
                <div v-if="similarProducts.length > 0" class="similar-products-section">
                    <div class="similar-header animate-fadeInUp">
                        <h2 class="similar-title">
                            {{ t({ ru: 'Похожие товары', kz: 'Ұқсас тауарлар' }) }}
                        </h2>
                        <p class="similar-subtitle">
                            {{ t({
                                ru: 'Другие товары в этой категории',
                                kz: 'Осы санаттағы басқа тауарлар',
                            }) }}
                        </p>
                    </div>
                    <div class="similar-products-grid">
                        <div
                            v-for="(similarProduct, index) in similarProducts"
                            :key="similarProduct.id"
                            class="animate-fadeInUp"
                            :style="{ animationDelay: `${index * 50}ms` }"
                        >
                            <ProductCard :product="similarProduct" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<style scoped>
/* Product Detail Page */
.product-detail-page {
    background: var(--smo-bg);
    min-height: 100vh;
    font-family: var(--font-body);
    padding-bottom: 4rem;
}

/* Breadcrumbs */
.breadcrumb-nav {
    margin-bottom: 2rem;
}

.breadcrumb-link {
    font-family: var(--font-body);
    color: var(--smo-text-secondary);
    transition: color var(--transition-base);
}

.breadcrumb-link:hover {
    color: var(--smo-primary);
}

.breadcrumb-current {
    font-family: var(--font-body);
    color: var(--smo-text-primary);
    font-weight: 600;
}

/* Product Grid */
.product-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
}

@media (min-width: 1024px) {
    .product-grid {
        grid-template-columns: 1fr 1fr;
    }
}

/* Gallery Section */
.gallery-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.main-image {
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: var(--radius-lg);
    border: 1px solid var(--smo-border);
    background: white;
    box-shadow: var(--shadow-md);
    position: relative;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform var(--transition-slow);
}

.main-image:hover .product-image {
    transform: scale(1.05);
}

.image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: var(--smo-text-muted);
    opacity: 0.2;
}

/* Thumbnails */
.thumbnails-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
}

.thumbnail {
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: var(--radius-md);
    border: 2px solid transparent;
    background: white;
    cursor: pointer;
    transition: all var(--transition-base);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail:hover {
    border-color: var(--smo-primary-light);
}

.thumbnail.active {
    border-color: var(--smo-primary);
    box-shadow: 0 0 0 3px rgba(44, 95, 93, 0.1);
}

/* Info Section */
.info-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Product Header */
.product-header {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.product-title {
    font-family: var(--font-display);
    font-size: 2rem;
    font-weight: 700;
    color: var(--smo-text-primary);
    line-height: 1.2;
}

.product-subtitle {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--smo-text-secondary);
}

.price-section {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.product-price {
    font-family: var(--font-display);
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--smo-primary);
}

.price-unit {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--smo-text-muted);
}

/* Divider */
.divider {
    height: 1px;
    background: var(--smo-border);
}

/* Description */
.description-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.section-heading {
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.description-text {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    color: var(--smo-text-secondary);
    line-height: 1.6;
}

/* Availability */
.availability-section {
    display: flex;
    align-items: center;
}

.availability-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: linear-gradient(135deg,
        rgba(74, 155, 127, 0.1) 0%,
        rgba(74, 155, 127, 0.05) 100%);
    border: 1px solid rgba(74, 155, 127, 0.3);
    border-radius: var(--radius-lg);
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-success);
}

.availability-badge.out-of-stock {
    background: linear-gradient(135deg,
        rgba(220, 38, 38, 0.1) 0%,
        rgba(220, 38, 38, 0.05) 100%);
    border-color: rgba(220, 38, 38, 0.3);
    color: #DC2626;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--smo-success);
}

.status-dot.unavailable {
    background: #DC2626;
}

/* Purchase Section */
.purchase-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.quantity-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quantity-label {
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-text-primary);
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.qty-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    border: 2px solid var(--smo-border);
    background: var(--smo-surface);
    color: var(--smo-text-primary);
    cursor: pointer;
    transition: all var(--transition-base);
}

.qty-btn:hover:not(:disabled) {
    border-color: var(--smo-primary);
    color: var(--smo-primary);
}

.qty-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.qty-input {
    width: 80px;
    height: 48px;
    text-align: center;
    border: 2px solid var(--smo-border);
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 700;
    padding: 0;
}

.add-to-cart-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    height: 56px;
    background: linear-gradient(135deg, var(--smo-accent) 0%, var(--smo-accent-light) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 1.0625rem;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition-base);
    box-shadow: 0 4px 14px rgba(217, 119, 87, 0.3);
}

.add-to-cart-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 87, 0.4);
}

.add-to-cart-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.add-to-cart-btn.success {
    background: linear-gradient(135deg, var(--smo-success) 0%, #5BAD8F 100%);
}

/* Shop Info Card */
.shop-info-card {
    background: linear-gradient(135deg,
        rgba(44, 95, 93, 0.05) 0%,
        rgba(44, 95, 93, 0.02) 100%);
    border: 1px solid rgba(44, 95, 93, 0.2);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
}

.shop-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.shop-icon {
    color: var(--smo-primary);
}

.shop-title {
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.shop-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.shop-name {
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 600;
    color: var(--smo-text-primary);
}

.shop-description {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--smo-text-secondary);
}

/* Specs Section */
.specs-section {
    margin-top: 4rem;
}

.specs-title {
    font-family: var(--font-display);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--smo-text-primary);
    margin-bottom: 1.5rem;
}

.specs-card {
    background: var(--smo-surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--smo-border);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.spec-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--smo-border);
}

.spec-row:last-child {
    border-bottom: none;
}

.spec-row:nth-child(even) {
    background: rgba(44, 95, 93, 0.02);
}

.spec-key {
    font-family: var(--font-body);
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--smo-text-secondary);
}

.spec-value {
    font-family: var(--font-display);
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--smo-text-primary);
}

/* Similar Products */
.similar-products-section {
    margin-top: 4rem;
}

.similar-header {
    margin-bottom: 2rem;
}

.similar-title {
    font-family: var(--font-display);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--smo-text-primary);
}

.similar-subtitle {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--smo-text-secondary);
    margin-top: 0.5rem;
}

.similar-products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

@media (min-width: 640px) {
    .similar-products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1024px) {
    .similar-products-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (min-width: 1280px) {
    .similar-products-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* Responsive */
@media (max-width: 640px) {
    .product-title {
        font-size: 1.5rem;
    }

    .product-price {
        font-size: 2rem;
    }

    .spec-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
}
</style>
