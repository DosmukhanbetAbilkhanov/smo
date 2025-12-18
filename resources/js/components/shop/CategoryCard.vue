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
    <div class="category-group">
        <Link
            v-if="!hasChildren"
            :href="`/categories/${category.slug}`"
            class="category-link category-parent-link"
        >
            {{ categoryName }}
        </Link>
        <button
            v-else
            @click="toggleExpanded"
            class="category-parent category-parent-button md:cursor-default"
            type="button"
        >
            <span>{{ categoryName }}</span>
            <svg
                class="chevron md:hidden"
                :class="{ expanded: isExpanded }"
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
            class="category-children"
            :class="{ 'mobile-collapsed': !isExpanded }"
        >
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

/* Parent category button for mobile collapsible */
.category-parent-button {
    width: 100%;
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
}

@media (min-width: 768px) {
    .category-parent-button {
        cursor: default;
        pointer-events: none;
    }
}

/* Chevron icon */
.chevron {
    width: 1.25rem;
    height: 1.25rem;
    transition: transform var(--transition-base);
    flex-shrink: 0;
    margin-left: 0.5rem;
}

.chevron.expanded {
    transform: rotate(180deg);
}

/* Children container */
.category-children {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    overflow: hidden;
    transition:
        max-height 0.3s ease-in-out,
        opacity 0.3s ease-in-out;
}

/* Mobile collapsed state */
@media (max-width: 767px) {
    .category-children.mobile-collapsed {
        max-height: 0;
        opacity: 0;
        margin-top: 0;
    }

    .category-children:not(.mobile-collapsed) {
        max-height: 500px;
        opacity: 1;
        margin-top: 0.25rem;
    }
}

/* Desktop - always show children */
@media (min-width: 768px) {
    .category-children {
        max-height: none;
        opacity: 1;
    }
}

/* Children categories - blue normal underlined */
.category-child {
    color: #2563eb;
    font-weight: 400;
    text-decoration: underline;
}
</style>
