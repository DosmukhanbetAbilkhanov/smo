<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { useLocale } from '@/composables/useLocale';
import type { Category } from '@/types/api';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

// Generate a color based on category ID for variety
const categoryColor = computed(() => {
    const colors = [
        'from-blue-500 to-blue-600',
        'from-green-500 to-green-600',
        'from-purple-500 to-purple-600',
        'from-orange-500 to-orange-600',
        'from-pink-500 to-pink-600',
        'from-teal-500 to-teal-600',
        'from-indigo-500 to-indigo-600',
        'from-red-500 to-red-600',
    ];
    return colors[props.category.id % colors.length];
});

const hasChildren = computed(
    () => props.category.children && props.category.children.length > 0,
);

const showChildren = ref(false);
const cardRef = ref<HTMLElement | null>(null);
const dropdownStyle = ref({});

const handleMouseEnter = () => {
    if (hasChildren.value && cardRef.value) {
        const rect = cardRef.value.getBoundingClientRect();
        dropdownStyle.value = {
            top: `${rect.bottom + window.scrollY + 8}px`,
            left: `${rect.left + window.scrollX}px`,
            width: `${rect.width}px`,
        };
        showChildren.value = true;
    }
};

const handleMouseLeave = () => {
    showChildren.value = false;
};
</script>

<template>
    <div
        ref="cardRef"
        class="group relative"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
    >
        <Link :href="`/categories/${category.slug}`">
            <Card
                class="h-full overflow-hidden transition-all hover:shadow-lg"
            >
                <CardContent class="p-0">
                    <!-- Colored Card Block (Image commented out for future use) -->
                    <div
                        class="relative aspect-video overflow-hidden bg-gradient-to-br"
                        :class="categoryColor"
                    >
                        <!-- Future image implementation -->
                        <!-- <img
                            :src="categoryImage"
                            :alt="categoryName"
                            class="h-full w-full object-cover transition-transform group-hover:scale-105"
                        /> -->

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"
                        />
                        <div
                            class="absolute right-0 bottom-0 left-0 p-4 text-white"
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
                                class="mt-1 line-clamp-2 text-sm text-white/90"
                            >
                                {{ categoryDescription }}
                            </p>
                            <p
                                v-if="hasChildren"
                                class="mt-2 text-xs text-white/80"
                            >
                                {{ category.children?.length }} subcategories
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </Link>

        <!-- Children Categories Dropdown using Teleport -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
            >
                <div
                    v-if="hasChildren && showChildren"
                    :style="dropdownStyle"
                    class="fixed z-[9999] rounded-lg border bg-popover p-3 text-popover-foreground shadow-xl"
                    @mouseenter="showChildren = true"
                    @mouseleave="showChildren = false"
                >
                    <div
                        class="mb-2 text-xs font-medium text-muted-foreground"
                    >
                        Subcategories:
                    </div>
                    <div class="space-y-1">
                        <Link
                            v-for="child in category.children"
                            :key="child.id"
                            :href="`/categories/${child.slug}`"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                        >
                            <ChevronRight :size="14" />
                            {{ getLocalizedName(child) }}
                        </Link>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
