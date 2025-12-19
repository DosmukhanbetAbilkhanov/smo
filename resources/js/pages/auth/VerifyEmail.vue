<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Form, Head } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        :title="t({ ru: 'Подтверждение email', kz: 'Email растау' })"
        :description="t({ ru: 'Пожалуйста, подтвердите ваш email адрес, нажав на ссылку, которую мы отправили вам на почту.', kz: 'Поштаңызға жіберілген сілтемені басу арқылы email мекенжайыңызды растаңыз.' })"
    >
        <Head :title="t({ ru: 'Подтверждение email', kz: 'Email растау' })" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ t({ ru: 'Новая ссылка для подтверждения была отправлена на email адрес, указанный при регистрации.', kz: 'Жаңа растау сілтемесі тіркелу кезінде көрсетілген email мекенжайына жіберілді.' }) }}
        </div>

        <Form
            v-bind="send.form()"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary">
                <Spinner v-if="processing" />
                {{ t({ ru: 'Отправить повторно', kz: 'Қайта жіберу' }) }}
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block text-sm"
            >
                {{ t({ ru: 'Выйти', kz: 'Шығу' }) }}
            </TextLink>
        </Form>
    </AuthLayout>
</template>
