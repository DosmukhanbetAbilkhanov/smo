<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();
</script>

<template>
    <AuthBase
        :title="t({ ru: 'Создать аккаунт', kz: 'Аккаунт жасау' })"
        :description="t({ ru: 'Введите данные для создания аккаунта', kz: 'Аккаунт жасау үшін деректерді енгізіңіз' })"
    >
        <Head :title="t({ ru: 'Регистрация', kz: 'Тіркеу' })" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">{{ t({ ru: 'Имя', kz: 'Аты' }) }}</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        :placeholder="t({ ru: 'Полное имя', kz: 'Толық аты' })"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">{{ t({ ru: 'Email адрес', kz: 'Email мекенжайы' }) }}</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        :placeholder="t({ ru: 'email@example.com', kz: 'email@example.com' })"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ t({ ru: 'Пароль', kz: 'Құпия сөз' }) }}</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        :placeholder="t({ ru: 'Пароль', kz: 'Құпия сөз' })"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">{{ t({ ru: 'Подтверждение пароля', kz: 'Құпия сөзді растау' }) }}</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        :placeholder="t({ ru: 'Подтвердите пароль', kz: 'Құпия сөзді растаңыз' })"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    {{ t({ ru: 'Создать аккаунт', kz: 'Аккаунт жасау' }) }}
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                {{ t({ ru: 'Уже есть аккаунт?', kz: 'Аккаунт бар ма?' }) }}
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >{{ t({ ru: 'Войти', kz: 'Кіру' }) }}</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
