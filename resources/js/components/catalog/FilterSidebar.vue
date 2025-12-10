<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { ProductFilters } from '@/types/api';
import { FilterX } from 'lucide-vue-next';
import { computed } from 'vue';
import PriceRangeFilter from './PriceRangeFilter.vue';

interface Props {
    filters: ProductFilters;
}

interface Emits {
    (e: 'update:filters', filters: ProductFilters): void;
    (e: 'clear'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const hasActiveFilters = computed(() => {
    return !!(
        props.filters.min_price ||
        props.filters.max_price ||
        props.filters.search ||
        props.filters.category_id
    );
});

function updatePriceRange({ min, max }: { min?: number; max?: number }) {
    emit('update:filters', {
        ...props.filters,
        min_price: min,
        max_price: max,
    });
}

function handleClearAll() {
    emit('clear');
}
</script>

<template>
    <Card class="sticky top-4">
        <CardHeader class="pb-3">
            <div class="flex items-center justify-between">
                <CardTitle class="text-lg">Filters</CardTitle>
                <Button
                    v-if="hasActiveFilters"
                    variant="ghost"
                    size="sm"
                    @click="handleClearAll"
                    class="gap-2"
                >
                    <FilterX :size="16" />
                    Clear All
                </Button>
            </div>
        </CardHeader>

        <CardContent class="space-y-6">
            <!-- Price Range Filter -->
            <div>
                <h3 class="mb-3 text-sm font-semibold">Price Range</h3>
                <PriceRangeFilter
                    :min-price="filters.min_price"
                    :max-price="filters.max_price"
                    @update="updatePriceRange"
                />
            </div>

            <Separator />

            <!-- Additional filters can be added here -->
            <slot name="additional-filters" />
        </CardContent>
    </Card>
</template>
