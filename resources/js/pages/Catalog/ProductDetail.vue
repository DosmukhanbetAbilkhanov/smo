<script setup lang="ts">
import ProductCard from '@/components/shop/ProductCard.vue';
import PriceDisplay from '@/components/shop/PriceDisplay.vue';
import QuantityControl from '@/components/shop/QuantityControl.vue';
import PageBreadcrumb from '@/components/PageBreadcrumb.vue';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { useCartStore } from '@/stores/cart';
import { useLocaleStore } from '@/stores/locale';
import type { Product } from '@/types/api';
import { Head } from '@inertiajs/vue3';
import { Check, Package, ShoppingCart, Store } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import LoginModal from '@/components/LoginModal.vue';

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
const showLoginModal = ref(false);

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

const breadcrumbItems = computed(() => {
    const items: Array<{ label?: string; href?: string; isCurrentPage?: boolean }> = [
        { label: t({ ru: 'Главная', kz: 'Басты бет' }), href: '/' },
        { label: t({ ru: 'Товары', kz: 'Тауарлар' }), href: '/products' },
    ];

    if (props.product.nomenclature?.category) {
        items.push({
            label: categoryName.value,
            href: `/categories/${props.product.nomenclature.category.slug}`,
        });
    }

    items.push({
        label: productName.value,
        isCurrentPage: true,
    });

    return items;
});
const unitName = computed(() => {
    const unit = props.product.nomenclature?.unit;
    if (!unit) return '';
    const locale = (window as any).locale || 'ru';
    return locale === 'kz' ? unit.shortname_kz : unit.shortname_ru;
});

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
    <Head :title="productName" />

    <ShopLayout>
        <div class="-mx-4 min-h-screen bg-concrete-50">
            <!-- Breadcrumbs -->
            <PageBreadcrumb :items="breadcrumbItems" />

            <!-- Product Detail -->
            <div class="px-4 sm:px-6 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Image Gallery -->
                    <div class="flex flex-col gap-4">
                        <!-- Main Image -->
                        <div class="aspect-square overflow-hidden rounded-xl border border-concrete-200 bg-white shadow-lg">
                            <img
                                v-if="selectedImage"
                                :src="selectedImage"
                                :alt="productName"
                                class="w-full h-full object-contain transition-transform duration-500 hover:scale-105"
                            />
                            <div v-else class="flex items-center justify-center w-full h-full text-concrete-400 opacity-20">
                                <Package :size="64" />
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        <div v-if="productImages.length > 1" class="grid grid-cols-4 gap-3">
                            <button
                                v-for="(image, index) in productImages"
                                :key="index"
                                @click="selectImage(index)"
                                :class="[
                                    'aspect-square overflow-hidden rounded-lg border-2 bg-white cursor-pointer transition-all duration-200',
                                    selectedImageIndex === index
                                        ? 'border-amber-500 ring-4 ring-amber-500/20'
                                        : 'border-transparent hover:border-amber-300'
                                ]"
                            >
                                <img :src="image" :alt="`${productName} - ${index + 1}`" class="w-full h-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="flex flex-col gap-6">
                        <!-- Title and Price -->
                        <div class="flex flex-col gap-3">
                            <h1 class="font-display text-3xl lg:text-4xl font-bold text-steel-900 leading-tight">
                                {{ productName }}
                            </h1>
                            <p v-if="nomenclatureName" class="font-body text-base text-concrete-600">
                                {{ nomenclatureName }}
                            </p>
                            <div class="flex items-baseline gap-2 mt-2">
                                <PriceDisplay :price="product.price" class="font-display text-4xl font-bold text-amber-600" />
                                <span class="font-body text-base text-concrete-500">/ {{ unitName }}</span>
                            </div>
                        </div>

                        <div class="h-px bg-concrete-200"></div>

                        <!-- Description -->
                        <div v-if="nomenclatureDescription" class="flex flex-col gap-3">
                            <h3 class="font-display text-lg font-bold text-steel-900">
                                {{ t({ ru: 'Описание', kz: 'Сипаттама' }) }}
                            </h3>
                            <p class="font-body text-base text-concrete-700 leading-relaxed">
                                {{ nomenclatureDescription }}
                            </p>
                        </div>

                        <!-- Availability -->
                        <div class="flex items-center">
                            <div
                                :class="[
                                    'inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border font-display text-sm font-semibold',
                                    isOutOfStock
                                        ? 'bg-gradient-to-br from-red-50 to-red-100 border-red-300 text-red-700'
                                        : 'bg-gradient-to-br from-lime-100 to-lime-200 border-lime-300 text-lime-700'
                                ]"
                            >
                                <span :class="['w-2 h-2 rounded-full', isOutOfStock ? 'bg-red-600' : 'bg-lime-600']" />
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

                        <div class="h-px bg-concrete-200"></div>

                        <!-- Quantity Selector and Add to Cart -->
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label class="font-display text-sm font-semibold text-steel-900">
                                    {{ t({ ru: 'Количество', kz: 'Саны' }) }}
                                </label>
                                <QuantityControl
                                    v-model="quantity"
                                    :min="1"
                                    :max="maxQuantity"
                                    :disabled="isOutOfStock"
                                    :editable="false"
                                    :large="true"
                                />
                            </div>

                            <button
                                @click="handleAddToCart"
                                :disabled="isOutOfStock || adding"
                                :class="[
                                    'flex items-center justify-center gap-3 font-display font-bold px-8 py-4 rounded-xl transition-all duration-200',
                                    showSuccess
                                        ? 'bg-forest-600 text-white hover:bg-forest-700 hover:shadow-industrial-lg hover:-translate-y-0.5'
                                        : 'bg-amber-500 text-white hover:bg-amber-600 hover:shadow-industrial-lg hover:-translate-y-0.5 disabled:bg-concrete-300 disabled:text-concrete-500 disabled:cursor-not-allowed disabled:hover:shadow-none disabled:hover:translate-y-0'
                                ]"
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

                        <div class="h-px bg-concrete-200"></div>

                        <!-- Shop Info -->
                        <div
                            v-if="product.shop"
                            class="bg-gradient-to-br from-steel-50 to-steel-100 border border-steel-300 rounded-xl p-6"
                        >
                            <div class="flex items-center gap-3 mb-4">
                                <Store :size="20" class="text-steel-700" />
                                <h3 class="font-display text-lg font-bold text-steel-900">
                                    {{ t({ ru: 'Продавец', kz: 'Сатушы' }) }}
                                </h3>
                            </div>
                            <div class="flex flex-col gap-2">
                                <p class="font-display text-base font-semibold text-steel-900">
                                    {{ product.shop.name }}
                                </p>
                                <p v-if="product.shop.description" class="font-body text-sm text-concrete-700">
                                    {{ product.shop.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Specs -->
            <div v-if="product.nomenclature?.specs && product.nomenclature.specs.length > 0" class="container mx-auto px-4 sm:px-6 py-8">
                <h2 class="font-display text-2xl lg:text-3xl font-bold text-steel-900 mb-6">
                    {{ t({ ru: 'Характеристики', kz: 'Сипаттамалар' }) }}
                </h2>
                <div class="bg-white rounded-xl border border-concrete-200 shadow-lg overflow-hidden">
                    <div
                        v-for="(spec, index) in product.nomenclature.specs"
                        :key="index"
                        :class="[
                            'grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-8 px-6 py-5 border-b border-concrete-200 last:border-b-0',
                            index % 2 === 1 ? 'bg-steel-50/30' : ''
                        ]"
                    >
                        <div class="font-body text-sm sm:text-base font-medium text-concrete-600">
                            {{ localeStore.currentLocale === 'ru' ? spec.key_ru : spec.key_kz }}
                        </div>
                        <div class="font-display text-sm sm:text-base font-semibold text-steel-900">
                            {{ localeStore.currentLocale === 'ru' ? spec.value_ru : spec.value_kz }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Products -->
            <div v-if="similarProducts.length > 0" class="container mx-auto px-4 sm:px-6 py-8">
                <div class="mb-8">
                    <h2 class="font-display text-2xl lg:text-3xl font-bold text-steel-900 border-l-4 border-amber-500 pl-4">
                        {{ t({ ru: 'Похожие товары', kz: 'Ұқсас тауарлар' }) }}
                    </h2>
                    <p class="font-body text-base text-concrete-600 mt-2 ml-5">
                        {{ t({
                            ru: 'Другие товары в этой категории',
                            kz: 'Осы санаттағы басқа тауарлар',
                        }) }}
                    </p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    <ProductCard
                        v-for="similarProduct in similarProducts"
                        :key="similarProduct.id"
                        :product="similarProduct"
                    />
                </div>
            </div>
        </div>

        <LoginModal v-model:open="showLoginModal" />
    </ShopLayout>
</template>

<style scoped>
/* Minimal custom styles - most styling is done with Tailwind CSS */
</style>
