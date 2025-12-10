<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useLocale } from '@/composables/useLocale';
import { useCatalogStore } from '@/stores/catalog';
import { Link } from '@inertiajs/vue3';
import { ChevronDown, Layers } from 'lucide-vue-next';
import { computed, onMounted } from 'vue';

const catalogStore = useCatalogStore();
const { getLocalizedName } = useLocale();

const topLevelCategories = computed(() =>
    catalogStore.categories.filter((cat) => !cat.parent_id),
);

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
                <Layers :size="16" />
                Categories
                <ChevronDown :size="14" />
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="start" class="w-56">
            <!-- All Categories Link -->
            <DropdownMenuItem as-child>
                <Link
                    href="/categories"
                    class="flex w-full cursor-pointer items-center"
                >
                    <Layers :size="16" class="mr-2" />
                    <span class="font-medium">All Categories</span>
                </Link>
            </DropdownMenuItem>

            <!-- Loading State -->
            <template v-if="catalogStore.loading">
                <DropdownMenuItem disabled>
                    <span class="text-sm text-muted-foreground"
                        >Loading...</span
                    >
                </DropdownMenuItem>
            </template>

            <!-- Categories List -->
            <template v-else>
                <DropdownMenuItem
                    v-for="category in topLevelCategories"
                    :key="category.id"
                    as-child
                >
                    <Link
                        :href="`/categories/${category.slug}`"
                        class="flex w-full cursor-pointer items-center"
                    >
                        {{ getLocalizedName(category) }}
                    </Link>
                </DropdownMenuItem>
            </template>

            <!-- Empty State -->
            <DropdownMenuItem
                v-if="!catalogStore.loading && topLevelCategories.length === 0"
                disabled
            >
                <span class="text-sm text-muted-foreground"
                    >No categories found</span
                >
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
