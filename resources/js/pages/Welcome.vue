<script setup lang="ts">
import CategoryCard from '@/components/shop/CategoryCard.vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category, Product } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Package, ShoppingBag, Truck } from 'lucide-vue-next';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();

interface Props {
    featuredCategories: Category[];
    popularProducts: Product[];
}

defineProps<Props>();
</script>

<template>
    <Head :title="t({ ru: 'Главная', kz: 'Басты бет' })" />

    <ShopLayout>
        <!-- Hero Section -->
        <section class="relative border-b-4 border-amber-500 bg-gradient-to-br from-steel-50 via-concrete-50 to-amber-50 overflow-hidden">
            <!-- Decorative Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: linear-gradient(90deg, #2d3a3a 1px, transparent 1px), linear-gradient(#2d3a3a 1px, transparent 1px); background-size: 40px 40px;"></div>
            </div>

            <div class="container mx-auto px-6 py-6 lg:py-10 relative z-10">
                <div class="mx-auto max-w-4xl text-center">
                    <h1 class="font-display text-3xl font-bold tracking-tight text-steel-900 lg:text-4xl mb-3">
                        {{ t({ ru: 'Маркетплейс строительных материалов', kz: 'Құрылыс материалдарының маркетплейсі' }) }}
                    </h1>
                    <p class="font-body text-base text-steel-700 lg:text-lg leading-relaxed max-w-3xl mx-auto">
                        {{ t({ ru: 'Находите качественные строительные материалы от проверенных продавцов по всему Казахстану. Быстрая доставка, конкурентные цены и надежный сервис.', kz: 'Қазақстан бойынша тексерілген сатушылардан сапалы құрылыс материалдарын табыңыз. Жылдам жеткізу, бәсекеге қабілетті бағалар және сенімді қызмет.' }) }}
                    </p>

                    <!-- Feature Highlights -->
                    <div class="mt-4 grid gap-2 sm:grid-cols-3 max-w-3xl mx-auto">
                        <div class="flex flex-col items-center text-center p-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 shadow-industrial-md">
                                <Package :size="20" class="text-white" stroke-width="2" />
                            </div>
                            <h3 class="font-display mt-2 text-sm font-bold text-steel-900">{{ t({ ru: 'Широкий выбор', kz: 'Кең таңдау' }) }}</h3>
                            <p class="font-body mt-1 text-xs text-concrete-600">
                                {{ t({ ru: 'Тысячи товаров от надежных поставщиков', kz: 'Сенімді жеткізушілерден мыңдаған тауарлар' }) }}
                            </p>
                        </div>
                        <div class="flex flex-col items-center text-center p-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-rust-500 to-rust-600 shadow-industrial-md">
                                <Truck :size="20" class="text-white" stroke-width="2" />
                            </div>
                            <h3 class="font-display mt-2 text-sm font-bold text-steel-900">{{ t({ ru: 'Быстрая доставка', kz: 'Жылдам жеткізу' }) }}</h3>
                            <p class="font-body mt-1 text-xs text-concrete-600">
                                {{ t({ ru: 'Оперативная доставка по всему Казахстану', kz: 'Қазақстан бойынша жедел жеткізу' }) }}
                            </p>
                        </div>
                        <div class="flex flex-col items-center text-center p-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-forest-500 to-forest-600 shadow-industrial-md">
                                <ShoppingBag :size="20" class="text-white" stroke-width="2" />
                            </div>
                            <h3 class="font-display mt-2 text-sm font-bold text-steel-900">{{ t({ ru: 'Простой заказ', kz: 'Қарапайым тапсырыс' }) }}</h3>
                            <p class="font-body mt-1 text-xs text-concrete-600">
                                {{ t({ ru: 'Удобное оформление с безопасной оплатой', kz: 'Қауіпсіз төлеммен ыңғайлы рәсімдеу' }) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-6">
                <div class="mb-8">
                    <h2 class="font-display text-3xl font-bold text-steel-900 border-l-4 border-amber-500 pl-4">
                        {{ t({ ru: 'Категории', kz: 'Санаттар' }) }}
                    </h2>
                </div>

                <div class="grid gap-2 grid-cols-2 sm:grid-cols-3 lg:grid-cols-8">
                    <CategoryCard
                        v-for="category in featuredCategories"
                        :key="category.id"
                        :category="category"
                    />
                </div>
                <div class="flex justify-end mt-6">
                    <Link
                        href="/categories"
                        class="inline-flex items-center gap-2 font-display font-semibold text-steel-700 hover:text-amber-600 transition-colors duration-200"
                    >
                        {{ t({ ru: 'Смотреть все', kz: 'Барлығын көру' }) }}
                        <ArrowRight :size="16" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Popular Products -->
        <section class="border-t border-concrete-200 bg-concrete-50 py-16">
            <div class="container mx-auto px-6">
                <div class="mb-10">
                    <h2 class="font-display text-3xl font-bold text-steel-900 border-l-4 border-rust-500 pl-4 mb-3">
                        {{ t({ ru: 'Популярные товары', kz: 'Танымал тауарлар' }) }}
                    </h2>
                    <p class="font-body text-concrete-600 pl-5">
                        {{ t({ ru: 'Посмотрите наши самые популярные товары', kz: 'Біздің ең танымал тауарларды қараңыз' }) }}
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <ProductCard
                        v-for="product in popularProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>
                <div class="flex justify-end mt-8">
                    <Link
                        href="/products"
                        class="inline-flex items-center gap-2 font-display font-semibold text-steel-700 hover:text-amber-600 transition-colors duration-200"
                    >
                        {{ t({ ru: 'Смотреть все', kz: 'Барлығын көру' }) }}
                        <ArrowRight :size="16" />
                    </Link>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>
