<script setup lang="ts">
import { useLocale } from '@/composables/useLocale';
import type { Category } from '@/types/api';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    category: Category;
}

const props = defineProps<Props>();

const { getLocalizedName } = useLocale();

const categoryName = computed(() => getLocalizedName(props.category));

const hasChildren = computed(
    () => props.category.children && props.category.children.length > 0,
);

const isExpanded = ref(false);

const toggleExpanded = () => {
    if (hasChildren.value) {
        isExpanded.value = !isExpanded.value;
    }
};
</script>

<template>
    <div class="flex flex-col gap-1">
        <Link
            v-if="!hasChildren"
            :href="`/categories/${category.slug}`"
            class="font-display text-sm font-bold text-steel-900 no-underline cursor-pointer transition-colors duration-200 hover:text-amber-600"
        >
            {{ categoryName }}
        </Link>
        <button
            v-else
            @click="toggleExpanded"
            class="w-full text-left flex justify-between items-center bg-none border-none p-0 font-display text-sm font-bold text-steel-900 mb-1 cursor-pointer md:cursor-default md:pointer-events-none"
            type="button"
        >
            <span>{{ categoryName }}</span>
            <svg
                class="w-5 h-5 flex-shrink-0 ml-2 transition-transform duration-200 md:hidden"
                :class="{ 'rotate-180': isExpanded }"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>

        <div
            v-if="hasChildren"
            class="flex flex-col gap-1 overflow-hidden transition-all duration-300 ease-in-out md:max-h-none md:opacity-100"
            :class="isExpanded ? 'max-h-[500px] opacity-100 mt-1 md:mt-0' : 'max-h-0 opacity-0 mt-0 md:opacity-100 md:max-h-none'"
        >
            <Link
                v-for="child in category.children"
                :key="child.id"
                :href="`/categories/${child.slug}`"
                class="font-body text-sm text-steel-700 underline decoration-steel-400 underline-offset-2 no-underline-hover cursor-pointer transition-colors duration-200 hover:text-amber-600 hover:decoration-amber-600"
            >
                {{ getLocalizedName(child) }}
            </Link>
        </div>
    </div>
</template>
