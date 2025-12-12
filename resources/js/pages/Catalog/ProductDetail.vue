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
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { useCartStore } from '@/stores/cart';
import type { Product } from '@/types/api';
import { Head } from '@inertiajs/vue3';
import { Minus, Package, Plus, ShoppingCart, Store } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    product: Product;
    similarProducts: Product[];
}

const props = defineProps<Props>();
const { getLocalizedName, getLocalizedDescription, getLocalizedValue, t } = useLocale();
const cartStore = useCartStore();

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
    <Head :title="productName" />

    <ShopLayout>
        <div class="container py-6">
            <!-- Breadcrumbs -->
            <Breadcrumb class="mb-6">
                <BreadcrumbList>
                    <BreadcrumbItem>
                        <BreadcrumbLink href="/">
                            {{ t({ ru: 'Главная', kz: 'Басты бет' }) }}
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbLink href="/products">
                            {{ t({ ru: 'Товары', kz: 'Тауарлар' }) }}
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator v-if="product.nomenclature?.category" />
                    <BreadcrumbItem v-if="product.nomenclature?.category">
                        <BreadcrumbLink
                            :href="`/categories/${product.nomenclature.category.slug}`"
                        >
                            {{ categoryName }}
                        </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <BreadcrumbItem>
                        <BreadcrumbPage>{{ productName }}</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>

            <!-- Product Detail -->
            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Image Gallery -->
                <div class="space-y-4">
                    <!-- Main Image -->
                    <div
                        class="relative aspect-square overflow-hidden rounded-lg border bg-muted"
                    >
                        <img
                            v-if="selectedImage"
                            :src="selectedImage"
                            :alt="productName"
                            class="h-full w-full object-contain"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <Package :size="64" class="text-muted-foreground/20" />
                        </div>
                    </div>

                    <!-- Thumbnail Gallery -->
                    <div
                        v-if="productImages.length > 1"
                        class="grid grid-cols-4 gap-2"
                    >
                        <button
                            v-for="(image, index) in productImages"
                            :key="index"
                            @click="selectImage(index)"
                            :class="[
                                'relative aspect-square overflow-hidden rounded-md border-2 transition-all',
                                selectedImageIndex === index
                                    ? 'border-primary'
                                    : 'border-transparent hover:border-muted-foreground/50',
                            ]"
                        >
                            <img
                                :src="image"
                                :alt="`${productName} - ${index + 1}`"
                                class="h-full w-full object-cover"
                            />
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-6">
                    <!-- Title and Price -->
                    <div>
                        <h1 class="text-3xl font-bold">{{ productName }}</h1>
                        <p
                            v-if="nomenclatureName"
                            class="mt-2 text-muted-foreground"
                        >
                            {{ nomenclatureName }}
                        </p>
                        <div class="mt-4">
                            <PriceDisplay
                                :price="product.price"
                                class="text-4xl"
                            />
                            <span class="ml-2 text-muted-foreground">
                                / {{ unitName }}
                            </span>
                        </div>
                    </div>

                    <Separator />

                    <!-- Description -->
                    <div v-if="nomenclatureDescription">
                        <h3 class="mb-2 font-semibold">
                            {{ t({ ru: 'Описание', kz: 'Сипаттама' }) }}
                        </h3>
                        <p class="text-muted-foreground">
                            {{ nomenclatureDescription }}
                        </p>
                    </div>

                    <!-- Availability -->
                    <div>
                        <span
                            :class="[
                                'inline-flex items-center gap-2 rounded-md px-3 py-1 text-sm font-medium',
                                isOutOfStock
                                    ? 'bg-destructive/10 text-destructive'
                                    : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            ]"
                        >
                            <span
                                :class="[
                                    'h-2 w-2 rounded-full',
                                    isOutOfStock ? 'bg-destructive' : 'bg-green-500',
                                ]"
                            />
                            <span v-if="isOutOfStock">
                                {{ t({ ru: 'Нет в наличии', kz: 'Қолжетімсіз' }) }}
                            </span>
                            <span v-else>
                                {{ t({
                                    ru: `В наличии: ${product.quantity} ${unitName}`,
                                    kz: `Қолжетімді: ${product.quantity} ${unitName}`,
                                }) }}
                            </span>
                        </span>
                    </div>

                    <Separator />

                    <!-- Quantity Selector and Add to Cart -->
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                {{ t({ ru: 'Количество', kz: 'Саны' }) }}
                            </label>
                            <div class="flex items-center gap-3">
                                <Button
                                    @click="decrementQuantity"
                                    :disabled="quantity <= 1 || isOutOfStock"
                                    variant="outline"
                                    size="icon"
                                >
                                    <Minus :size="16" />
                                </Button>
                                <Input
                                    v-model.number="quantity"
                                    type="number"
                                    :min="1"
                                    :max="maxQuantity"
                                    :disabled="isOutOfStock"
                                    class="w-20 text-center"
                                />
                                <Button
                                    @click="incrementQuantity"
                                    :disabled="quantity >= maxQuantity || isOutOfStock"
                                    variant="outline"
                                    size="icon"
                                >
                                    <Plus :size="16" />
                                </Button>
                            </div>
                        </div>

                        <Button
                            @click="handleAddToCart"
                            :disabled="isOutOfStock || adding"
                            size="lg"
                            class="w-full gap-2"
                        >
                            <ShoppingCart :size="20" />
                            <span v-if="showSuccess">
                                {{ t({ ru: '✓ Добавлено!', kz: '✓ Қосылды!' }) }}
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
                        </Button>
                    </div>

                    <Separator />

                    <!-- Shop Info -->
                    <Card v-if="product.shop">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-lg">
                                <Store :size="20" />
                                {{ t({ ru: 'Продавец', kz: 'Сатушы' }) }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <p class="font-semibold">{{ product.shop.name }}</p>
                                <p
                                    v-if="product.shop.description"
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ product.shop.description }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Product Specs -->
            <div v-if="product.specs && product.specs.length > 0" class="mt-12">
                <h2 class="mb-6 text-2xl font-bold">
                    {{ t({ ru: 'Характеристики', kz: 'Сипаттамалар' }) }}
                </h2>
                <Card>
                    <CardContent class="p-0">
                        <div class="divide-y">
                            <div
                                v-for="(spec, index) in product.specs"
                                :key="index"
                                class="grid grid-cols-2 gap-4 p-4"
                            >
                                <div class="font-medium text-muted-foreground">
                                    {{ getLocalizedValue(spec, 'key') }}
                                </div>
                                <div>
                                    {{ getLocalizedValue(spec, 'value') }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Similar Products -->
            <div v-if="similarProducts.length > 0" class="mt-12">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">
                        {{ t({ ru: 'Похожие товары', kz: 'Ұқсас тауарлар' }) }}
                    </h2>
                    <p class="mt-2 text-muted-foreground">
                        {{ t({
                            ru: 'Другие товары в этой категории',
                            kz: 'Осы санаттағы басқа тауарлар',
                        }) }}
                    </p>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                    <ProductCard
                        v-for="similarProduct in similarProducts"
                        :key="similarProduct.id"
                        :product="similarProduct"
                    />
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
