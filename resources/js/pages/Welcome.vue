<script setup lang="ts">
import CategoryCard from '@/components/shop/CategoryCard.vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import { Button } from '@/components/ui/button';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category, Product } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Package, ShoppingBag, Truck } from 'lucide-vue-next';

interface Props {
    featuredCategories: Category[];
    popularProducts: Product[];
}

defineProps<Props>();
</script>

<template>
    <Head title="Home" />

    <ShopLayout>
        <!-- Hero Section -->
        <section
            class="border-b bg-gradient-to-br from-primary/5 to-primary/10"
        >
            <div class="container mx-auto px-4 py-4 lg:py-24">
                <div class="mx-auto max-w-3xl text-center">
                    <h1 class="text-2xl font-bold tracking-tight lg:text-4xl">
                        Building Materials Marketplace
                    </h1>
                    <p class="mt-6 text-lg text-muted-foreground lg:text-xl">
                        Find quality building materials from verified sellers
                        across Kazakhstan. Fast delivery, competitive prices,
                        and reliable service.
                    </p>
                    <!-- <div
                        class="mt-10 flex flex-wrap items-center justify-center gap-4"
                    >
                        <Button size="lg" as-child>
                            <Link href="/categories">
                                Browse Categories
                                <ArrowRight :size="18" class="ml-2" />
                            </Link>
                        </Button>
                        <Button size="lg" variant="outline" as-child>
                            <Link href="/products"> View All Products </Link>
                        </Button>
                    </div> -->
                </div>

                <!-- Features -->
                <!-- <div class="mt-16 grid gap-8 md:grid-cols-3">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <Package :size="24" class="text-primary" />
                        </div>
                        <h3 class="mt-4 font-semibold">Wide Selection</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Thousands of products from trusted suppliers
                        </p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <Truck :size="24" class="text-primary" />
                        </div>
                        <h3 class="mt-4 font-semibold">Fast Delivery</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Quick and reliable delivery to your location
                        </p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <ShoppingBag :size="24" class="text-primary" />
                        </div>
                        <h3 class="mt-4 font-semibold">Easy Ordering</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Simple checkout process with secure payments
                        </p>
                    </div>
                </div> -->
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold">Browse by Category</h2>
                        <p class="mt-2 text-muted-foreground">
                            Explore our wide range of building materials
                        </p>
                    </div>
                    <Button variant="ghost" as-child>
                        <Link href="/categories">
                            View All
                            <ArrowRight :size="16" class="ml-2" />
                        </Link>
                    </Button>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <CategoryCard
                        v-for="category in featuredCategories"
                        :key="category.id"
                        :category="category"
                    />
                </div>
            </div>
        </section>

        <!-- Popular Products -->
        <section class="border-t bg-muted/50 py-16">
            <div class="container mx-auto px-4">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold">Popular Products</h2>
                        <p class="mt-2 text-muted-foreground">
                            Check out our most popular items
                        </p>
                    </div>
                    <Button variant="ghost" as-child>
                        <Link href="/products">
                            View All
                            <ArrowRight :size="16" class="ml-2" />
                        </Link>
                    </Button>
                </div>

                <div
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <ProductCard
                        v-for="product in popularProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </div>
        </section>
    </ShopLayout>
</template>
