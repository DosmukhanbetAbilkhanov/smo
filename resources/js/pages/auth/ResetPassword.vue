<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <AuthLayout
        :title="t({ ru: 'Сброс пароля', kz: 'Құпия сөзді қалпына келтіру' })"
        :description="t({ ru: 'Пожалуйста, введите новый пароль ниже', kz: 'Төменде жаңа құпия сөзді енгізіңіз' })"
    >
        <Head :title="t({ ru: 'Сброс пароля', kz: 'Құпия сөзді қалпына келтіру' })" />

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">{{ t({ ru: 'Email', kz: 'Email' }) }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="inputEmail"
                        class="mt-1 block w-full"
                        readonly
                    />
                    <InputError :message="errors.email" class="mt-2" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ t({ ru: 'Пароль', kz: 'Құпия сөз' }) }}</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        autofocus
                        :placeholder="t({ ru: 'Пароль', kz: 'Құпия сөз' })"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">
                        {{ t({ ru: 'Подтверждение пароля', kz: 'Құпия сөзді растау' }) }}
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        :placeholder="t({ ru: 'Подтвердите пароль', kz: 'Құпия сөзді растаңыз' })"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :disabled="processing"
                    data-test="reset-password-button"
                >
                    <Spinner v-if="processing" />
                    {{ t({ ru: 'Сбросить пароль', kz: 'Құпия сөзді қалпына келтіру' }) }}
                </Button>
            </div>
        </Form>
    </AuthLayout>
</template>
