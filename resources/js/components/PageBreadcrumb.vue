<script setup lang="ts">
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Link } from '@inertiajs/vue3';

interface BreadcrumbItem {
    label?: string;
    href?: string;
    icon?: any;
    isCurrentPage?: boolean;
}

interface Props {
    items: BreadcrumbItem[];
    variant?: 'default' | 'minimal';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
});
</script>

<template>
    <div
        :class="[
            '-mx-4',
            variant === 'default' ? 'bg-[var(--smo-surface)] border-b border-[var(--smo-border)]' : ''
        ]"
    >
        <div class="px-4 py-4">
            <Breadcrumb>
                <BreadcrumbList>
                    <template v-for="(item, index) in items" :key="index">
                        <BreadcrumbItem>
                            <BreadcrumbLink
                                v-if="!item.isCurrentPage && item.href"
                                as-child
                            >
                                <Link
                                    :href="item.href"
                                    class="text-[var(--smo-text-secondary)] transition-colors hover:text-[var(--smo-primary)] font-[var(--font-body)]"
                                >
                                    <component v-if="item.icon" :is="item.icon" :size="16" />
                                    <span v-else>{{ item.label }}</span>
                                </Link>
                            </BreadcrumbLink>
                            <BreadcrumbPage
                                v-else
                                class="text-[var(--smo-text-primary)] font-semibold font-[var(--font-body)]"
                            >
                                {{ item.label }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="index < items.length - 1" />
                    </template>
                </BreadcrumbList>
            </Breadcrumb>
        </div>
    </div>
</template>
