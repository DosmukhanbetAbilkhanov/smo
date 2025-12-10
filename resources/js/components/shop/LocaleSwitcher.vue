<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useLocale } from '@/composables/useLocale';
import { Globe } from 'lucide-vue-next';

const { currentLocale, oppositeLocaleDisplayName, toggleLocale } = useLocale();
</script>

<template>
    <Button
        variant="ghost"
        size="sm"
        @click="toggleLocale"
        :title="`Switch to ${oppositeLocaleDisplayName}`"
        class="gap-2"
    >
        <Globe :size="16" />
        <span class="hidden sm:inline">{{ currentLocale.toUpperCase() }}</span>
    </Button>
</template>
