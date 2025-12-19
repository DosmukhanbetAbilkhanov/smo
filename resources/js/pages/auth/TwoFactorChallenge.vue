<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    PinInput,
    PinInputGroup,
    PinInputSlot,
} from '@/components/ui/pin-input';
import AuthLayout from '@/layouts/AuthLayout.vue';
// Dummy store for 2FA login (2FA is disabled)
const store = {
    form: () => ({ action: '/two-factor-challenge', method: 'post' }),
};
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();

interface AuthConfigContent {
    title: string;
    description: string;
    toggleText: string;
}

const authConfigContent = computed<AuthConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: t({ ru: 'Код восстановления', kz: 'Қалпына келтіру коды' }),
            description: t({
                ru: 'Пожалуйста, подтвердите доступ к аккаунту, введя один из резервных кодов восстановления.',
                kz: 'Резервтік қалпына келтіру кодтарының бірін енгізу арқылы аккаунтқа қол жеткізуді растаңыз.'
            }),
            toggleText: t({ ru: 'войти с помощью кода аутентификации', kz: 'аутентификация кодын пайдаланып кіру' }),
        };
    }

    return {
        title: t({ ru: 'Код аутентификации', kz: 'Аутентификация коды' }),
        description: t({
            ru: 'Введите код аутентификации из вашего приложения аутентификатора.',
            kz: 'Аутентификатор қолданбасынан аутентификация кодын енгізіңіз.'
        }),
        toggleText: t({ ru: 'войти с помощью кода восстановления', kz: 'қалпына келтіру кодын пайдаланып кіру' }),
    };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = [];
};

const code = ref<number[]>([]);
const codeValue = computed<string>(() => code.value.join(''));
</script>

<template>
    <AuthLayout
        :title="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <Head :title="t({ ru: 'Двухфакторная аутентификация', kz: 'Екі факторлы аутентификация' })" />

        <div class="space-y-6">
            <template v-if="!showRecoveryInput">
                <Form
                    v-bind="store.form()"
                    class="space-y-4"
                    reset-on-error
                    @error="code = []"
                    #default="{ errors, processing, clearErrors }"
                >
                    <input type="hidden" name="code" :value="codeValue" />
                    <div
                        class="flex flex-col items-center justify-center space-y-3 text-center"
                    >
                        <div class="flex w-full items-center justify-center">
                            <PinInput
                                id="otp"
                                placeholder="○"
                                v-model="code"
                                type="number"
                                otp
                            >
                                <PinInputGroup>
                                    <PinInputSlot
                                        v-for="(id, index) in 6"
                                        :key="id"
                                        :index="index"
                                        :disabled="processing"
                                        autofocus
                                    />
                                </PinInputGroup>
                            </PinInput>
                        </div>
                        <InputError :message="errors.code" />
                    </div>
                    <Button type="submit" class="w-full" :disabled="processing"
                        >{{ t({ ru: 'Продолжить', kz: 'Жалғастыру' }) }}</Button
                    >
                    <div class="text-center text-sm text-muted-foreground">
                        <span>{{ t({ ru: 'или вы можете ', kz: 'немесе сіз ' }) }}</span>
                        <button
                            type="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </Form>
            </template>

            <template v-else>
                <Form
                    v-bind="store.form()"
                    class="space-y-4"
                    reset-on-error
                    #default="{ errors, processing, clearErrors }"
                >
                    <Input
                        name="recovery_code"
                        type="text"
                        :placeholder="t({ ru: 'Введите код восстановления', kz: 'Қалпына келтіру кодын енгізіңіз' })"
                        :autofocus="showRecoveryInput"
                        required
                    />
                    <InputError :message="errors.recovery_code" />
                    <Button type="submit" class="w-full" :disabled="processing"
                        >{{ t({ ru: 'Продолжить', kz: 'Жалғастыру' }) }}</Button
                    >

                    <div class="text-center text-sm text-muted-foreground">
                        <span>{{ t({ ru: 'или вы можете ', kz: 'немесе сіз ' }) }}</span>
                        <button
                            type="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </Form>
            </template>
        </div>
    </AuthLayout>
</template>
