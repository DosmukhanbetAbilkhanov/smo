<script setup lang="ts">
import CategoryCard from '@/components/shop/CategoryCard.vue';
import ProductCard from '@/components/shop/ProductCard.vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Button } from '@/components/ui/button';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category, PaginatedResponse, Product } from '@/types/api';
import { Head, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Home } from 'lucide-vue-next';

interface Props {
    category: Category;
    products: PaginatedResponse<Product>;
}

const props = defineProps<Props>();
const { getLocalizedName, getLocalizedDescription, t } = useLocale();

const categoryName = getLocalizedName(props.category);
const categoryDescription = getLocalizedDescription(props.category);

function goToPage(page: number) {
    router.get(
        `/categories/${props.category.slug}`,
        { page },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <Head :title="categoryName" />

    <ShopLayout>
        <!-- Breadcrumb -->
        <div class="border-b bg-muted/30">
            <div class="container mx-auto px-4 py-4">
                <Breadcrumb>
                    <BreadcrumbList>
                        <BreadcrumbItem>
                            <BreadcrumbLink as-child>
                                <Link href="/">
                                    <Home :size="16" />
                                </Link>
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbLink as-child>
                                <Link href="/categories">
                                    {{ t({ ru: 'Категории', kz: 'Санаттар' }) }}
                                </Link>
                            </BreadcrumbLink>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator />
                        <BreadcrumbItem>
                            <BreadcrumbPage>
                                {{ categoryName }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>
            </div>
        </div>

        <!-- Category Header -->
        <section class="border-b bg-muted/30 py-8">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold">{{ categoryName }}</h1>
                <p
                    v-if="categoryDescription"
                    class="mt-2 text-lg text-muted-foreground"
                >
                    {{ categoryDescription }}
                </p>
            </div>
        </section>

        <!-- Subcategories (if any) -->
        <section
            v-if="category.children && category.children.length > 0"
            class="border-b py-8"
        >
            <div class="container mx-auto px-4">
                <h2 class="mb-6 text-2xl font-semibold">
                    {{ t({ ru: 'Подкатегории', kz: 'Санаттар' }) }}
                </h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <CategoryCard
                        v-for="child in category.children"
                        :key="child.id"
                        :category="child"
                    />
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-semibold">
                        {{ t({ ru: 'Товары', kz: 'Өнімдер' }) }}
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        {{
                            t({
                                ru: `Найдено: ${products.total}`,
                                kz: `Табылды: ${products.total}`,
                            })
                        }}
                    </p>
                </div>

                <!-- Products Grid -->
                <div
                    v-if="products.data.length > 0"
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <ProductCard
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="flex min-h-[400px] items-center justify-center rounded-lg border border-dashed"
                >
                    <div class="text-center">
                        <p class="text-lg text-muted-foreground">
                            {{
                                t({
                                    ru: 'Товары не найдены',
                                    kz: 'Өнімдер табылмады',
                                })
                            }}
                        </p>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="products.last_page > 1"
                    class="mt-8 flex items-center justify-center gap-2"
                >
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="products.current_page === 1"
                        @click="goToPage(products.current_page - 1)"
                    >
                        <ChevronLeft :size="16" />
                        {{
                            t({
                                ru: 'Предыдущая',
                                kz: 'Алдыңғы',
                            })
                        }}
                    </Button>

                    <span class="text-sm text-muted-foreground">
                        {{
                            t({
                                ru: `Страница ${products.current_page} из ${products.last_page}`,
                                kz: `${products.current_page} бет ${products.last_page}`,
                            })
                        }}
                    </span>

                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="products.current_page === products.last_page"
                        @click="goToPage(products.current_page + 1)"
                    >
                        {{
                            t({
                                ru: 'Следующая',
                                kz: 'Келесі',
                            })
                        }}
                        <ChevronRight :size="16" />
                    </Button>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>
