<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref, watch } from 'vue';

interface Props {
    minPrice?: number;
    maxPrice?: number;
}

interface Emits {
    (e: 'update', value: { min?: number; max?: number }): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const localMin = ref(props.minPrice?.toString() || '');
const localMax = ref(props.maxPrice?.toString() || '');

function handleApply() {
    const min = localMin.value ? parseFloat(localMin.value) : undefined;
    const max = localMax.value ? parseFloat(localMax.value) : undefined;

    emit('update', { min, max });
}

function handleClear() {
    localMin.value = '';
    localMax.value = '';
    emit('update', { min: undefined, max: undefined });
}

// Watch for external changes
watch(
    () => props.minPrice,
    (val) => {
        localMin.value = val?.toString() || '';
    },
);

watch(
    () => props.maxPrice,
    (val) => {
        localMax.value = val?.toString() || '';
    },
);
</script>

<template>
    <div class="space-y-4">
        <div class="space-y-2">
            <Label for="min-price">Min Price (₸)</Label>
            <Input
                id="min-price"
                v-model="localMin"
                type="number"
                min="0"
                step="100"
                placeholder="0"
                @keyup.enter="handleApply"
            />
        </div>

        <div class="space-y-2">
            <Label for="max-price">Max Price (₸)</Label>
            <Input
                id="max-price"
                v-model="localMax"
                type="number"
                min="0"
                step="100"
                placeholder="No limit"
                @keyup.enter="handleApply"
            />
        </div>

        <div class="flex gap-2">
            <Button @click="handleApply" size="sm" class="flex-1">
                Apply
            </Button>
            <Button
                @click="handleClear"
                variant="outline"
                size="sm"
                class="flex-1"
            >
                Clear
            </Button>
        </div>
    </div>
</template>
