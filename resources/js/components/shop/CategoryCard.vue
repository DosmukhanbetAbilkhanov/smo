<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { useLocale } from '@/composables/useLocale';
import type { Category } from '@/types/api';
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Layers } from 'lucide-vue-next';
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

// Warm, lighter tone palette - architectural materials aesthetic
const categoryTheme = computed(() => {
    const themes = [
        { bg: '#FFF4E6', accent: '#FFB84D', shadow: 'rgba(255, 184, 77, 0.15)' }, // Warm Apricot
        { bg: '#FFE8D6', accent: '#FF9B70', shadow: 'rgba(255, 155, 112, 0.15)' }, // Soft Peach
        { bg: '#F5E6D3', accent: '#D4A574', shadow: 'rgba(212, 165, 116, 0.15)' }, // Sand
        { bg: '#FFE5E0', accent: '#FF9B8A', shadow: 'rgba(255, 155, 138, 0.15)' }, // Dusty Rose
        { bg: '#FFF0E5', accent: '#FFAA7F', shadow: 'rgba(255, 170, 127, 0.15)' }, // Warm Cream
        { bg: '#F9E8D9', accent: '#D89B6A', shadow: 'rgba(216, 155, 106, 0.15)' }, // Warm Beige
        { bg: '#FFE6DA', accent: '#FF9470', shadow: 'rgba(255, 148, 112, 0.15)' }, // Terracotta Light
        { bg: '#FFF5E8', accent: '#FFC285', shadow: 'rgba(255, 194, 133, 0.15)' }, // Honey
    ];
    return themes[props.category.id % themes.length];
});

const hasChildren = computed(
    () => props.category.children && props.category.children.length > 0,
);

const isFlipped = ref(false);
const isHovered = ref(false);
</script>

<template>
    <div
        class="category-card-container"
        @mouseenter="isFlipped = hasChildren ? true : false; isHovered = true"
        @mouseleave="isFlipped = false; isHovered = false"
    >
        <div
            class="category-card-flip"
            :class="{ 'is-flipped': isFlipped }"
        >
            <!-- Front Side -->
            <div class="card-face card-front">
                <Link :href="`/categories/${category.slug}`">
                    <div
                        class="card-content"
                        :class="{ 'is-hovered': isHovered }"
                        :style="{
                            '--card-bg': categoryTheme.bg,
                            '--card-accent': categoryTheme.accent,
                            '--card-shadow': categoryTheme.shadow,
                        }"
                    >
                        <!-- Decorative Corner Accent -->
                        <div class="corner-accent" />

                        <!-- Subtle Pattern Texture -->
                        <div class="card-texture" />

                        <!-- Future image implementation -->
                        <!-- <img
                            :src="categoryImage"
                            :alt="categoryName"
                            class="category-image"
                        /> -->

                        <!-- Content -->
                        <div class="card-body">
                            <div class="card-header">
                                <div class="category-icon">
                                    <Layers :size="28" :stroke-width="1.5" />
                                </div>
                            </div>

                            <div class="card-footer">
                                <h3 class="category-title">
                                    {{ categoryName }}
                                </h3>

                                <p
                                    v-if="showDescription && categoryDescription"
                                    class="category-description"
                                >
                                    {{ categoryDescription }}
                                </p>

                                <div class="category-meta">
                                    <span v-if="hasChildren" class="subcategory-count">
                                        <Layers :size="14" :stroke-width="2" />
                                        {{ category.children?.length }} subcategories
                                    </span>
                                    <ChevronRight :size="20" class="arrow-icon" />
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Back Side (Children List) -->
            <div
                v-if="hasChildren"
                class="card-face card-back"
            >
                <div
                    class="card-content back-content"
                    :style="{
                        '--card-bg': categoryTheme.bg,
                        '--card-accent': categoryTheme.accent,
                    }"
                >
                    <div class="back-header">
                        <h3 class="back-title">{{ categoryName }}</h3>
                        <Link
                            :href="`/categories/${category.slug}`"
                            class="view-all-link"
                        >
                            View All
                        </Link>
                    </div>

                    <div class="subcategories-list">
                        <Link
                            v-for="child in category.children"
                            :key="child.id"
                            :href="`/categories/${child.slug}`"
                            class="subcategory-item"
                        >
                            <ChevronRight :size="16" :stroke-width="2" />
                            <span>{{ getLocalizedName(child) }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ========================================
   WARM ARCHITECTURAL MINIMALISM DESIGN
   Inspired by: Sun-baked clay, natural terracotta,
   soft architectural materials
   ======================================== */

.category-card-container {
    perspective: 1200px;
    height: 280px;
}

.category-card-flip {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 600ms cubic-bezier(0.4, 0, 0.2, 1);
    transform-style: preserve-3d;
}

.category-card-flip.is-flipped {
    transform: rotateY(180deg);
}

.card-face {
    position: absolute;
    inset: 0;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.card-back {
    transform: rotateY(180deg);
}

/* ========================================
   FRONT CARD DESIGN
   ======================================== */

.card-content {
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--card-bg);
    border-radius: 16px;
    overflow: hidden;
    transition: all 350ms cubic-bezier(0.4, 0, 0.2, 1);

    /* Warm, soft shadow - like sunlight on architectural materials */
    box-shadow:
        0 1px 3px rgba(0, 0, 0, 0.04),
        0 8px 24px var(--card-shadow),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.card-content.is-hovered {
    transform: translateY(-6px) scale(1.01);
    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.06),
        0 16px 48px var(--card-shadow),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
}

/* Decorative corner accent - architectural detail */
.corner-accent {
    position: absolute;
    top: 0;
    right: 0;
    width: 80px;
    height: 80px;
    background: var(--card-accent);
    opacity: 0.12;
    clip-path: polygon(100% 0, 100% 100%, 0 0);
    transition: all 350ms cubic-bezier(0.4, 0, 0.2, 1);
}

.card-content.is-hovered .corner-accent {
    width: 100px;
    height: 100px;
    opacity: 0.18;
}

/* Subtle texture pattern - paper/material texture */
.card-texture {
    position: absolute;
    inset: 0;
    opacity: 0.03;
    background-image:
        repeating-linear-gradient(
            45deg,
            transparent,
            transparent 2px,
            var(--card-accent) 2px,
            var(--card-accent) 4px
        );
    pointer-events: none;
    mix-blend-mode: overlay;
}

/* Future image styling */
.category-image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 600ms cubic-bezier(0.4, 0, 0.2, 1);
}

.card-content.is-hovered .category-image {
    transform: scale(1.05);
}

/* ========================================
   CARD CONTENT LAYOUT
   ======================================== */

.card-body {
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 1.75rem;
    z-index: 1;
}

.card-header {
    flex: 0 0 auto;
    margin-bottom: auto;
}

.category-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(8px);
    border-radius: 14px;
    color: var(--card-accent);
    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transition: all 350ms cubic-bezier(0.4, 0, 0.2, 1);
}

.card-content.is-hovered .category-icon {
    background: rgba(255, 255, 255, 0.8);
    transform: translateY(-3px) rotate(5deg);
    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 1);
}

.card-footer {
    flex: 0 0 auto;
}

/* ========================================
   TYPOGRAPHY
   ======================================== */

.category-title {
    font-family: var(--font-display);
    font-size: 1.375rem;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.01em;
    color: #2A2A2A;
    margin-bottom: 0.5rem;
    transition: color 250ms ease;
}

.card-content.is-hovered .category-title {
    color: #1A1A1A;
}

.category-description {
    font-family: var(--font-body);
    font-size: 0.875rem;
    line-height: 1.5;
    color: #5A5A5A;
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.category-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1.5px solid rgba(0, 0, 0, 0.06);
}

.subcategory-count {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-family: var(--font-display);
    font-size: 0.8125rem;
    font-weight: 600;
    color: #6A6A6A;
    letter-spacing: 0.01em;
}

.arrow-icon {
    color: var(--card-accent);
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.card-content.is-hovered .arrow-icon {
    transform: translateX(4px);
    color: var(--card-accent);
}

/* ========================================
   BACK CARD DESIGN
   ======================================== */

.back-content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    box-shadow:
        0 1px 3px rgba(0, 0, 0, 0.04),
        0 8px 24px var(--card-shadow);
}

.back-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 2px solid rgba(0, 0, 0, 0.08);
}

.back-title {
    font-family: var(--font-display);
    font-size: 1.125rem;
    font-weight: 700;
    color: #2A2A2A;
    letter-spacing: -0.01em;
}

.view-all-link {
    font-family: var(--font-display);
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--card-accent);
    text-decoration: none;
    transition: all 200ms ease;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.5);
}

.view-all-link:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: translateX(2px);
}

.subcategories-list {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.375rem;

    /* Custom scrollbar for warm aesthetic */
    scrollbar-width: thin;
    scrollbar-color: var(--card-accent) transparent;
}

.subcategories-list::-webkit-scrollbar {
    width: 6px;
}

.subcategories-list::-webkit-scrollbar-track {
    background: transparent;
}

.subcategories-list::-webkit-scrollbar-thumb {
    background: var(--card-accent);
    border-radius: 3px;
    opacity: 0.3;
}

.subcategory-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 0.875rem;
    border-radius: 10px;
    font-family: var(--font-body);
    font-size: 0.875rem;
    font-weight: 500;
    color: #3A3A3A;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.4);
    transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
    border: 1.5px solid transparent;
}

.subcategory-item:hover {
    background: rgba(255, 255, 255, 0.8);
    border-color: var(--card-accent);
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.subcategory-item svg {
    flex-shrink: 0;
    color: var(--card-accent);
    transition: transform 200ms ease;
}

.subcategory-item:hover svg {
    transform: translateX(2px);
}

.subcategory-item span {
    flex: 1;
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

@media (max-width: 640px) {
    .category-card-container {
        height: 260px;
    }

    .card-body {
        padding: 1.5rem;
    }

    .category-icon {
        width: 48px;
        height: 48px;
    }

    .category-title {
        font-size: 1.25rem;
    }

    .category-description {
        font-size: 0.8125rem;
    }
}

/* ========================================
   ANIMATION REFINEMENTS
   ======================================== */

@media (prefers-reduced-motion: reduce) {
    .category-card-flip,
    .card-content,
    .category-icon,
    .arrow-icon,
    .subcategory-item {
        transition: none;
    }
}
</style>
