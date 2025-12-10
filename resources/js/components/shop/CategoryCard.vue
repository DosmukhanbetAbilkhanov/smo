<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { useLocale } from '@/composables/useLocale';
import type { Category } from '@/types/api';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    category: Category;
    showDescription?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showDescription: false,
});

const { getLocalizedName, getLocalizedDescription } = useLocale();

const categoryName = computed(() => getLocalizedName(props.category));
const categoryDescription = computed(() =>
    getLocalizedDescription(props.category),
);
const categoryImage = computed(
    () => props.category.image || '/images/placeholder-category.jpg',
);
</script>

<template>
    <Link :href="`/categories/${category.slug}`">
        <Card
            class="group h-full overflow-hidden transition-all hover:shadow-lg"
        >
            <CardContent class="p-0">
                <div class="relative aspect-video overflow-hidden bg-muted">
                    <img
                        :src="categoryImage"
                        :alt="categoryName"
                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent"
                    />
                    <div
                        class="absolute right-0 bottom-0 left-0 p-4 text-foreground"
                    >
                        <h3
                            class="flex items-center justify-between text-lg font-semibold"
                        >
                            {{ categoryName }}
                            <ChevronRight
                                :size="20"
                                class="transition-transform group-hover:translate-x-1"
                            />
                        </h3>
                        <p
                            v-if="showDescription && categoryDescription"
                            class="mt-1 line-clamp-2 text-sm text-muted-foreground"
                        >
                            {{ categoryDescription }}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </Link>
</template>
