<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Minus, Plus, Trash2 } from 'lucide-vue-next';

interface Props {
    modelValue: number;
    min?: number;
    max?: number;
    disabled?: boolean;
    editable?: boolean;
    showRemove?: boolean;
    large?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    min: 1,
    max: undefined,
    disabled: false,
    editable: true,
    showRemove: false,
    large: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: number];
    'remove': [];
}>();

function handleDecrease() {
    if (props.disabled || props.modelValue <= props.min) return;
    emit('update:modelValue', props.modelValue - 1);
}

function handleIncrease() {
    if (props.disabled) return;
    if (props.max !== undefined && props.modelValue >= props.max) return;
    emit('update:modelValue', props.modelValue + 1);
}

function handleInput(event: Event) {
    const value = parseInt((event.target as HTMLInputElement).value);
    if (isNaN(value)) return;

    let newValue = value;
    if (newValue < props.min) newValue = props.min;
    if (props.max !== undefined && newValue > props.max) newValue = props.max;

    emit('update:modelValue', newValue);
}

function handleRemove() {
    if (props.disabled) return;
    emit('remove');
}
</script>

<template>
    <div class="flex items-center gap-3">
        <button
            @click="handleDecrease"
            :disabled="disabled || modelValue <= min"
            :class="[
                'flex items-center justify-center flex-shrink-0 font-display font-bold bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-steel-700',
                large ? 'w-12 h-12' : 'w-8 h-8'
            ]"
        >
            <Minus :size="16" />
        </button>

        <Input
            v-if="editable"
            :value="modelValue"
            @input="handleInput"
            type="number"
            :min="min"
            :max="max"
            :disabled="disabled"
            :class="[
                'text-center rounded-lg font-display p-0 bg-white border-2 border-concrete-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20',
                large ? 'w-20 h-12 text-lg font-bold' : 'w-16 h-8 text-sm font-semibold'
            ]"
        />
        <div
            v-else
            :class="[
                'flex flex-1 items-center justify-center rounded-lg bg-white px-3 font-display text-steel-900',
                large ? 'text-lg font-bold h-12' : 'text-sm font-semibold'
            ]"
        >
            {{ modelValue }}
        </div>

        <button
            @click="handleIncrease"
            :disabled="disabled || (max !== undefined && modelValue >= max)"
            :class="[
                'flex items-center justify-center flex-shrink-0 font-display font-bold bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-steel-700',
                large ? 'w-12 h-12' : 'w-8 h-8'
            ]"
        >
            <Plus :size="16" />
        </button>

        <button
            v-if="showRemove"
            @click="handleRemove"
            :disabled="disabled"
            class="flex items-center justify-center flex-shrink-0 w-8 h-8 font-display font-bold bg-transparent text-rust-600 border-2 border-rust-600 rounded-lg hover:bg-rust-600 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-rust-600"
        >
            <Trash2 :size="16" />
        </button>
    </div>
</template>
