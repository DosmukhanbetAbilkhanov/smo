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
        <div class="breadcrumb-bar">
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

        <!-- Categories Page -->
        <div class="categories-page bg-pattern">
            <div class="page-container">
                <!-- Page Header -->
                <div class="page-header animate-fadeInUp">
                    <div class="header-content">
                        <h1 class="page-title">
                            {{ t({ ru: 'Все категории', kz: 'Барлық санаттар' }) }}
                        </h1>
                        <div class="categories-badge">
                            <Grid3x3 :size="16" />
                            <span>{{ categories.length }} {{ t({ ru: 'категорий', kz: 'санат' }) }}</span>
                        </div>
                    </div>
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
                <div v-if="categories.length > 0" class="categories-grid">
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
                <div v-else class="empty-state animate-fadeInUp">
                    <div class="empty-icon">
                        <FolderOpen :size="64" />
                    </div>
                    <h3 class="empty-title">
                        {{ t({ ru: 'Категории не найдены', kz: 'Санаттар табылмады' }) }}
                    </h3>
                    <p class="empty-text">
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

<style scoped>
/* Breadcrumb Bar */
.breadcrumb-bar {
    background: var(--smo-surface);
    border-bottom: 1px solid var(--smo-border);
}

/* Categories Page */
.categories-page {
    min-height: 100vh;
    background: var(--smo-bg);
}

/* Header Content */
.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}

.categories-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, rgba(44, 95, 93, 0.1) 0%, rgba(44, 95, 93, 0.05) 100%);
    border: 1px solid rgba(44, 95, 93, 0.2);
    border-radius: var(--radius-md);
    font-family: var(--font-display);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-primary);
}

/* Categories Grid */
.categories-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 640px) {
    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .categories-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1280px) {
    .categories-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    padding: 3rem 1.5rem;
    background: var(--smo-surface);
    border: 2px dashed var(--smo-border);
    border-radius: var(--radius-lg);
    text-align: center;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 120px;
    height: 120px;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, rgba(44, 95, 93, 0.1) 0%, rgba(44, 95, 93, 0.05) 100%);
    border-radius: 50%;
    color: var(--smo-primary);
}

.empty-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--smo-text-primary);
    margin-bottom: 0.5rem;
}

.empty-text {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--smo-text-secondary);
    max-width: 400px;
}
</style>
