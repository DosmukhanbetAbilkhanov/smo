<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { Package, ArrowLeft } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const { t } = useLocale();
const mounted = ref(false);

defineProps<{
    status?: string;
}>();

onMounted(() => {
    mounted.value = true;
});
</script>

<template>
    <Head :title="t({ ru: 'Забыли пароль', kz: 'Құпия сөзді ұмыттыңыз ба' })" />

    <div class="min-h-screen bg-gradient-to-br from-concrete-100 via-white to-amber-50 flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Decorative Background Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(90deg, #2d3a3a 1px, transparent 1px), linear-gradient(#2d3a3a 1px, transparent 1px); background-size: 40px 40px;"></div>

        <!-- Main Card -->
        <div class="w-full max-w-md relative z-10" :class="{ 'animate-fadeInUp': mounted }">
            <!-- Logo/Brand -->
            <div class="flex justify-center mb-8">
                <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-2xl shadow-industrial-md border border-concrete-200">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-steel-100 to-steel-50 rounded-lg text-steel-700">
                        <Package :size="22" :stroke-width="2.5" />
                    </div>
                    <span class="font-display text-2xl font-bold text-steel-900">SMO</span>
                </div>
            </div>

            <!-- Forgot Password Card -->
            <div class="bg-white rounded-3xl shadow-industrial-xl border border-concrete-200 p-8 sm:p-10">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="font-display text-3xl font-bold text-steel-900 mb-2">
                        {{ t({ ru: 'Забыли пароль?', kz: 'Құпия сөзді ұмыттыңыз ба?' }) }}
                    </h1>
                    <p class="font-body text-concrete-600">
                        {{ t({ ru: 'Введите email для получения ссылки сброса пароля', kz: 'Құпия сөзді қалпына келтіру сілтемесін алу үшін email енгізіңіз' }) }}
                    </p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-6 flex items-center gap-3 p-4 bg-forest-50 border-l-4 border-forest-500 rounded-lg">
                    <svg class="w-5 h-5 text-forest-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-body text-sm font-medium text-forest-800">{{ status }}</span>
                </div>

                <!-- Form -->
                <Form v-bind="email.form()" v-slot="{ errors, processing }" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="font-display font-semibold text-steel-800 mb-2 block">
                            {{ t({ ru: 'Email адрес', kz: 'Email мекенжайы' }) }}
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            :class="{ 'border-rust-500 focus:border-rust-500 focus:ring-rust-500/20': errors.email }"
                            autocomplete="off"
                            autofocus
                            :placeholder="t({ ru: 'email@example.com', kz: 'email@example.com' })"
                        />
                        <InputError :message="errors.email" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl transition-all duration-200 hover:bg-amber-600 hover:shadow-industrial-lg hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        :disabled="processing"
                        data-test="email-password-reset-link-button"
                    >
                        <Spinner v-if="processing" class="w-5 h-5" />
                        <span v-if="!processing">{{ t({ ru: 'Отправить ссылку', kz: 'Сілтемені жіберу' }) }}</span>
                        <span v-else>{{ t({ ru: 'Отправка...', kz: 'Жіберілуде...' }) }}</span>
                    </button>

                    <!-- Back to Login Link -->
                    <div class="text-center pt-4 border-t border-concrete-200">
                        <TextLink
                            :href="login()"
                            class="font-body font-semibold text-steel-700 hover:text-amber-600 transition-colors duration-200 inline-flex items-center gap-2"
                        >
                            <ArrowLeft :size="16" />
                            {{ t({ ru: 'Вернуться к входу', kz: 'Кіруге қайту' }) }}
                        </TextLink>
                    </div>
                </Form>
            </div>

            <!-- Footer Text -->
            <p class="font-body text-center text-sm text-concrete-500 mt-6">
                {{ t({ ru: 'Защищено шифрованием', kz: 'Шифрлаумен қорғалған' }) }}
            </p>
        </div>
    </div>
</template>
