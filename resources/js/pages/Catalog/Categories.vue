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
import { FolderOpen, Grid3x3, Home } from 'lucide-vue-next';

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
        <div class="-mx-4 bg-[var(--smo-surface)]">
            <div class="px-4 py-4">
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

        <!-- Categories Page -->
        <div class="-mx-4 min-h-screen bg-[var(--smo-bg)] bg-pattern">
            <div class="px-4 py-8">
                <!-- Page Header -->
                <div class="mb-8 animate-fadeInUp">
                   
                    <p class="page-subtitle">
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
                    class="grid gap-2 grid-cols-2 sm:grid-cols-3 lg:grid-cols-8"
                >
                    <div
                        v-for="(category, index) in categories"
                        :key="category.id"
                        class="animate-fadeInUp"
                        :style="{ animationDelay: `${index * 50}ms` }"
                    >
                        <CategoryCard :category="category" :show-description="true" />
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center min-h-[400px] p-12 bg-[var(--smo-surface)] border-2 border-dashed border-[var(--smo-border)] rounded-[var(--radius-lg)] text-center animate-fadeInUp"
                >
                    <div
                        class="flex items-center justify-center w-[120px] h-[120px] mb-6 bg-gradient-to-br from-[rgba(44,95,93,0.1)] to-[rgba(44,95,93,0.05)] rounded-full text-[var(--smo-primary)]"
                    >
                        <FolderOpen :size="64" />
                    </div>
                    <h3 class="font-[var(--font-display)] text-2xl font-bold text-[var(--smo-text-primary)] mb-2">
                        {{ t({ ru: 'Категории не найдены', kz: 'Санаттар табылмады' }) }}
                    </h3>
                    <p class="font-[var(--font-body)] text-base text-[var(--smo-text-secondary)] max-w-[400px]">
                        {{
                            t({
                                ru: 'В данный момент категории отсутствуют',
                                kz: 'Қазіргі уақытта санаттар жоқ',
                            })
                        }}
                    </p>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
