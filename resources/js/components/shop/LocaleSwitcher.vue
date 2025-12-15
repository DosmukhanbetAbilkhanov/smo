<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useLocale } from '@/composables/useLocale';
import { computed } from 'vue';

const { currentLocale, oppositeLocaleDisplayName, toggleLocale } = useLocale();

const flagEmoji = computed(() => {
    return currentLocale.value === 'ru' ? '🇷🇺' : '🇰🇿';
});
</script>

<template>
    <Button
        variant="ghost"
        size="sm"
        @click="toggleLocale"
        :title="`Switch to ${oppositeLocaleDisplayName}`"
        class="gap-2"
    >
        <span class="text-xl leading-none">{{ flagEmoji }}</span>
        <span class="hidden sm:inline">{{ currentLocale.toUpperCase() }}</span>
    </Button>
</template>
