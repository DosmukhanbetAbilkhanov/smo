<script setup lang="ts">
import { useLocale } from '@/composables/useLocale';
import type { Category } from '@/types/api';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    category: Category;
}

const props = defineProps<Props>();

const { getLocalizedName } = useLocale();

const categoryName = computed(() => getLocalizedName(props.category));

const hasChildren = computed(
    () => props.category.children && props.category.children.length > 0,
);
</script>

<template>
    <div class="category-group">
        <Link
            v-if="!hasChildren"
            :href="`/categories/${category.slug}`"
            class="category-link category-parent-link"
        >
            {{ categoryName }}
        </Link>
        <span v-else class="category-parent">
            {{ categoryName }}
        </span>

        <div v-if="hasChildren" class="category-children">
            <Link
                v-for="child in category.children"
                :key="child.id"
                :href="`/categories/${child.slug}`"
                class="category-link category-child"
            >
                {{ getLocalizedName(child) }}
            </Link>
        </div>
    </div>
</template>

<style scoped>
.category-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.category-link {
    font-family: var(--font-body);
    font-size: 0.875rem;
    cursor: pointer;
    transition: opacity var(--transition-base);
    text-decoration: none;
    display: block;
}

.category-link:hover {
    opacity: 0.7;
}

/* Parent categories that are clickable (no children) */
.category-parent-link {
    color: #000;
    font-weight: 700;
}

/* Parent categories - black bold text (non-clickable) */
.category-parent {
    color: #000;
    font-weight: 700;
    margin-bottom: 0.25rem;
    font-family: var(--font-body);
    font-size: 0.875rem;
}

/* Children container */
.category-children {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

/* Children categories - blue normal underlined */
.category-child {
    color: #2563eb;
    font-weight: 400;
    text-decoration: underline;
}
</style>
