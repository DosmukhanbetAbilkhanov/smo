<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useLocale } from '@/composables/useLocale';
import { useCatalogStore } from '@/stores/catalog';
import type { Category } from '@/types/api';
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Menu } from 'lucide-vue-next';
import { computed, onMounted } from 'vue';

const catalogStore = useCatalogStore();
const { getLocalizedName, t } = useLocale();

const topLevelCategories = computed(() =>
    catalogStore.categories.filter((cat) => !cat.parent_id),
);

const hasChildren = (category: Category) => {
    return category.children && category.children.length > 0;
};

onMounted(async () => {
    // Load categories if not already loaded
    if (catalogStore.categories.length === 0) {
        try {
            await catalogStore.fetchCategories();
        } catch (error) {
            console.error('Failed to load categories:', error);
        }
    }
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="sm" class="gap-2">
                <Menu :size="20" />
                {{ t({ ru: 'Каталог', kz: 'Каталог' }) }}
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="start" class="w-56">
            <!-- All Categories Link -->
            <DropdownMenuItem as-child>
                <Link
                    href="/categories"
                    class="flex w-full cursor-pointer items-center"
                >
                    <Menu :size="16" class="mr-2" />
                    <span class="font-medium">{{ t({ ru: 'Все категории', kz: 'Барлық санаттар' }) }}</span>
                </Link>
            </DropdownMenuItem>

            <!-- Loading State -->
            <template v-if="catalogStore.loading">
                <DropdownMenuItem disabled>
                    <span class="text-sm text-muted-foreground">
                        {{ t({ ru: 'Загрузка...', kz: 'Жүктелуде...' }) }}
                    </span>
                </DropdownMenuItem>
            </template>

            <!-- Categories List with Children Support -->
            <template v-else>
                <template
                    v-for="category in topLevelCategories"
                    :key="category.id"
                >
                    <!-- Category with Children - Use Submenu -->
                    <DropdownMenuSub v-if="hasChildren(category)">
                        <DropdownMenuSubTrigger>
                            {{ getLocalizedName(category) }}
                        </DropdownMenuSubTrigger>
                        <DropdownMenuSubContent class="w-48">
                            <!-- Parent Category Link -->
                            <DropdownMenuItem as-child>
                                <Link
                                    :href="`/categories/${category.slug}`"
                                    class="flex w-full cursor-pointer items-center font-medium"
                                >
                                    <Menu :size="14" class="mr-2" />
                                    {{ t({ ru: 'Все', kz: 'Барлығы' }) }}
                                </Link>
                            </DropdownMenuItem>

                            <!-- Children Categories -->
                            <DropdownMenuItem
                                v-for="child in category.children"
                                :key="child.id"
                                as-child
                            >
                                <Link
                                    :href="`/categories/${child.slug}`"
                                    class="flex w-full cursor-pointer items-center"
                                >
                                    {{ getLocalizedName(child) }}
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuSubContent>
                    </DropdownMenuSub>

                    <!-- Category without Children - Direct Link -->
                    <DropdownMenuItem v-else as-child>
                        <Link
                            :href="`/categories/${category.slug}`"
                            class="flex w-full cursor-pointer items-center"
                        >
                            {{ getLocalizedName(category) }}
                        </Link>
                    </DropdownMenuItem>
                </template>
            </template>

            <!-- Empty State -->
            <DropdownMenuItem
                v-if="!catalogStore.loading && topLevelCategories.length === 0"
                disabled
            >
                <span class="text-sm text-muted-foreground">
                    {{ t({ ru: 'Категории не найдены', kz: 'Санаттар табылмады' }) }}
                </span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
