<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { Package } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const { t } = useLocale();
const mounted = ref(false);

onMounted(() => {
    mounted.value = true;
});
</script>

<template>
    <Head :title="t({ ru: 'Регистрация', kz: 'Тіркеу' })" />

    <div class="min-h-screen bg-gradient-to-br from-concrete-100 via-white to-rust-50 flex items-center justify-center p-4 relative overflow-hidden">
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

            <!-- Register Card -->
            <div class="bg-white rounded-3xl shadow-industrial-xl border border-concrete-200 p-8 sm:p-10">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="font-display text-3xl font-bold text-steel-900 mb-2">
                        {{ t({ ru: 'Создать аккаунт', kz: 'Аккаунт жасау' }) }}
                    </h1>
                    <p class="font-body text-concrete-600">
                        {{ t({ ru: 'Введите данные для создания аккаунта', kz: 'Аккаунт жасау үшін деректерді енгізіңіз' }) }}
                    </p>
                </div>

                <!-- Form -->
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="space-y-5"
                >
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="font-display font-semibold text-steel-800 mb-2 block">
                            {{ t({ ru: 'Имя', kz: 'Аты' }) }}
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            :class="{ 'border-rust-500 focus:border-rust-500 focus:ring-rust-500/20': errors.name }"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            :placeholder="t({ ru: 'Полное имя', kz: 'Толық аты' })"
                        />
                        <InputError :message="errors.name" class="mt-2" />
                    </div>

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
                            required
                            :tabindex="2"
                            autocomplete="email"
                            :placeholder="t({ ru: 'email@example.com', kz: 'email@example.com' })"
                        />
                        <InputError :message="errors.email" class="mt-2" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="font-display font-semibold text-steel-800 mb-2 block">
                            {{ t({ ru: 'Пароль', kz: 'Құпия сөз' }) }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            :class="{ 'border-rust-500 focus:border-rust-500 focus:ring-rust-500/20': errors.password }"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            :placeholder="t({ ru: 'Введите пароль', kz: 'Құпия сөзді енгізіңіз' })"
                        />
                        <InputError :message="errors.password" class="mt-2" />
                    </div>

                    <!-- Password Confirmation Field -->
                    <div>
                        <label for="password_confirmation" class="font-display font-semibold text-steel-800 mb-2 block">
                            {{ t({ ru: 'Подтверждение пароля', kz: 'Құпия сөзді растау' }) }}
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            :class="{ 'border-rust-500 focus:border-rust-500 focus:ring-rust-500/20': errors.password_confirmation }"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            :placeholder="t({ ru: 'Подтвердите пароль', kz: 'Құпия сөзді растаңыз' })"
                        />
                        <InputError :message="errors.password_confirmation" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl transition-all duration-200 hover:bg-amber-600 hover:shadow-industrial-lg hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none mt-6"
                        :tabindex="5"
                        :disabled="processing"
                        data-test="register-user-button"
                    >
                        <Spinner v-if="processing" class="w-5 h-5" />
                        <span v-if="!processing">{{ t({ ru: 'Создать аккаунт', kz: 'Аккаунт жасау' }) }}</span>
                        <span v-else>{{ t({ ru: 'Создание...', kz: 'Жасалуда...' }) }}</span>
                    </button>

                    <!-- Sign In Link -->
                    <div class="text-center pt-4 border-t border-concrete-200">
                        <span class="font-body text-concrete-600">{{ t({ ru: 'Уже есть аккаунт?', kz: 'Аккаунт бар ма?' }) }}</span>
                        <TextLink
                            :href="login()"
                            :tabindex="6"
                            class="font-body font-semibold text-amber-600 hover:text-amber-700 transition-colors duration-200 ml-2"
                        >
                            {{ t({ ru: 'Войти', kz: 'Кіру' }) }}
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
