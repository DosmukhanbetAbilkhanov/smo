<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, type HTMLAttributes } from 'vue';

interface Props {
    price: number;
    currency?: string;
    class?: HTMLAttributes['class'];
    showDecimals?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    currency: '₸', // Kazakhstani Tenge symbol
    showDecimals: true,
});

const formattedPrice = computed(() => {
    const price = props.showDecimals
        ? props.price.toFixed(2)
        : Math.round(props.price).toString();

    // Add thousand separators
    return price.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
});
</script>

<template>
    <span :class="cn('font-semibold text-foreground', props.class)">
        {{ formattedPrice }} {{ currency }}
    </span>
</template>
