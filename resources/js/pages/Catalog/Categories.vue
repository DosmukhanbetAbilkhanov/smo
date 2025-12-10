<script setup lang="ts">
import CategoryCard from '@/components/shop/CategoryCard.vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { useLocale } from '@/composables/useLocale';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Category } from '@/types/api';
import { Head, Link } from '@inertiajs/vue3';
import { Home } from 'lucide-vue-next';

interface Props {
    categories: Category[];
}

defineProps<Props>();
const { t } = useLocale();
</script>

<template>
    <Head title="Categories" />

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
                            <BreadcrumbPage>
                                {{ t({ ru: 'Категории', kz: 'Санаттар' }) }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                    </BreadcrumbList>
                </Breadcrumb>
            </div>
        </div>

        <!-- Categories Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="mb-8">
                    <h1 class="text-4xl font-bold">
                        {{ t({ ru: 'Все категории', kz: 'Барлық санаттар' }) }}
                    </h1>
                    <p class="mt-2 text-muted-foreground">
                        {{
                            t({
                                ru: 'Выберите категорию для просмотра товаров',
                                kz: 'Өнімдерді көру үшін санатты таңдаңыз',
                            })
                        }}
                    </p>
                </div>

                <!-- Categories Grid -->
                <div
                    v-if="categories.length > 0"
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <CategoryCard
                        v-for="category in categories"
                        :key="category.id"
                        :category="category"
                        :show-description="true"
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
                                    ru: 'Категории не найдены',
                                    kz: 'Санаттар табылмады',
                                })
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>
