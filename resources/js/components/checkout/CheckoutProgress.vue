<script setup lang="ts">
import { CheckCircle2, Package, ShoppingCart, Truck } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    currentStep: 1 | 2 | 3; // 1 = Cart, 2 = Delivery, 3 = Confirmation
}

const props = defineProps<Props>();

const steps = [
    { id: 1, label: 'Cart', icon: ShoppingCart },
    { id: 2, label: 'Delivery', icon: Truck },
    { id: 3, label: 'Confirmation', icon: Package },
];

const getStepStatus = (stepId: number) => {
    if (stepId < props.currentStep) return 'completed';
    if (stepId === props.currentStep) return 'active';
    return 'pending';
};

const isLineCompleted = (stepId: number) => {
    return stepId < props.currentStep;
};
</script>

<template>
    <div class="checkout-progress">
        <div class="container mx-auto px-4">
            <div class="progress-steps">
                <template v-for="(step, index) in steps" :key="step.id">
                    <div class="step" :class="getStepStatus(step.id)">
                        <div class="step-icon">
                            <CheckCircle2 v-if="getStepStatus(step.id) === 'completed'" :size="20" />
                            <component v-else :is="step.icon" :size="20" />
                        </div>
                        <span class="step-label">{{ step.label }}</span>
                    </div>
                    <div
                        v-if="index < steps.length - 1"
                        class="step-line"
                        :class="{ completed: isLineCompleted(steps[index + 1].id) }"
                    ></div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Progress Indicator */
.checkout-progress {
    background: var(--smo-surface, #FFFFFF);
    border-bottom: 1px solid var(--smo-border, #E5E5E5);
    padding: 1.5rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.progress-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    max-width: 600px;
    margin: 0 auto;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.step-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--smo-bg, #FAF9F6);
    border: 2px solid var(--smo-border, #E5E5E5);
    color: var(--smo-text-muted, #9E9E9E);
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.step.completed .step-icon {
    background: #4A9B7F;
    border-color: #4A9B7F;
    color: white;
}

.step.active .step-icon {
    background: var(--smo-primary, #2C5F5D);
    border-color: var(--smo-primary, #2C5F5D);
    color: white;
    box-shadow: 0 0 0 4px rgba(44, 95, 93, 0.1);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 4px rgba(44, 95, 93, 0.1);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(44, 95, 93, 0.05);
    }
}

.step-label {
    font-family: var(--font-display, 'Outfit', sans-serif);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--smo-text-muted, #9E9E9E);
    transition: color 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.step.completed .step-label,
.step.active .step-label {
    color: var(--smo-text-primary, #1A1A1A);
}

.step-line {
    width: 80px;
    height: 2px;
    background: var(--smo-border, #E5E5E5);
    margin: 0 1rem;
    position: relative;
    top: -1.25rem;
    transition: background 350ms cubic-bezier(0.4, 0, 0.2, 1);
}

.step-line.completed {
    background: #4A9B7F;
}

@media (max-width: 640px) {
    .step-label {
        font-size: 0.75rem;
    }

    .step-icon {
        width: 40px;
        height: 40px;
    }

    .step-line {
        width: 40px;
        top: -1rem;
    }
}
</style>
