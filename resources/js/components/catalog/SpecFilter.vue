<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { useLocale } from '@/composables/useLocale';
import type { ProductSpec } from '@/types/api';
import { computed } from 'vue';

interface SpecOption {
    spec: ProductSpec;
    count?: number;
}

interface Props {
    specKey: string;
    specKeyLocalized: string;
    options: SpecOption[];
    selectedValues?: string[];
}

interface Emits {
    (e: 'update', values: string[]): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const { currentLocale } = useLocale();

const localizedOptions = computed(() =>
    props.options.map((opt) => ({
        value: opt.spec.id.toString(),
        label:
            currentLocale.value === 'ru'
                ? opt.spec.value_ru
                : opt.spec.value_kz,
        count: opt.count,
    })),
);

function handleCheckboxChange(value: string, checked: boolean) {
    const currentValues = props.selectedValues || [];

    const newValues = checked
        ? [...currentValues, value]
        : currentValues.filter((v) => v !== value);

    emit('update', newValues);
}
</script>

<template>
    <div class="space-y-3">
        <h4 class="text-sm font-medium">{{ specKeyLocalized }}</h4>

        <div class="space-y-2">
            <div
                v-for="option in localizedOptions"
                :key="option.value"
                class="flex items-center gap-2"
            >
                <Checkbox
                    :id="`spec-${specKey}-${option.value}`"
                    :checked="selectedValues?.includes(option.value)"
                    @update:checked="
                        (checked) => handleCheckboxChange(option.value, checked)
                    "
                />
                <Label
                    :for="`spec-${specKey}-${option.value}`"
                    class="flex flex-1 cursor-pointer items-center justify-between text-sm font-normal"
                >
                    <span>{{ option.label }}</span>
                    <span v-if="option.count" class="text-muted-foreground">
                        ({{ option.count }})
                    </span>
                </Label>
            </div>
        </div>

        <div
            v-if="localizedOptions.length === 0"
            class="text-sm text-muted-foreground"
        >
            No options available
        </div>
    </div>
</template>
