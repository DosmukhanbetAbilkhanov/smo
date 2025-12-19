<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();
</script>

<template>
    <AuthLayout
        :title="t({ ru: 'Подтвердите пароль', kz: 'Құпия сөзді растаңыз' })"
        :description="t({ ru: 'Это защищенная область приложения. Пожалуйста, подтвердите пароль для продолжения.', kz: 'Бұл қолданбаның қорғалған аймағы. Жалғастыру үшін құпия сөзді растаңыз.' })"
    >
        <Head :title="t({ ru: 'Подтверждение пароля', kz: 'Құпия сөзді растау' })" />

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">{{ t({ ru: 'Пароль', kz: 'Құпия сөз' }) }}</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" />
                        {{ t({ ru: 'Подтвердить пароль', kz: 'Құпия сөзді растау' }) }}
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>
