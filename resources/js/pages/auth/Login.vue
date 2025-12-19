<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { useLocale } from '@/composables/useLocale';
import { Package } from 'lucide-vue-next';

const { t } = useLocale();

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const identifier = ref('');
const mounted = ref(false);

onMounted(() => {
    mounted.value = true;
});

// Detect if input is email or phone and update form fields accordingly
watch(identifier, (value) => {
    const emailInput = document.querySelector('input[name="email"]') as HTMLInputElement;
    const phoneInput = document.querySelector('input[name="phone"]') as HTMLInputElement;

    if (emailInput && phoneInput) {
        // Check if value looks like an email (contains @)
        if (value.includes('@')) {
            emailInput.value = value;
            phoneInput.value = '';
        } else {
            phoneInput.value = value;
            emailInput.value = '';
        }
    }
});
</script>

<template>
    <Head :title="t({ ru: 'Вход', kz: 'Кіру' })" />

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

            <!-- Login Card -->
            <div class="bg-white rounded-3xl shadow-industrial-xl border border-concrete-200 p-8 sm:p-10">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="font-display text-3xl font-bold text-steel-900 mb-2">
                        {{ t({ ru: 'Добро пожаловать!', kz: 'Қош келдіңіз!' }) }}
                    </h1>
                    <p class="font-body text-concrete-600">
                        {{ t({ ru: 'Войдите, чтобы продолжить', kz: 'Жалғастыру үшін кіріңіз' }) }}
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
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <!-- Email/Phone Field -->
                    <div>
                        <label for="identifier" class="font-display font-semibold text-steel-800 mb-2 block">
                            {{ t({ ru: 'Email или телефон', kz: 'Email немесе телефон' }) }}
                        </label>
                        <input
                            id="identifier"
                            v-model="identifier"
                            type="text"
                            class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            :class="{ 'border-rust-500 focus:border-rust-500 focus:ring-rust-500/20': errors.email || errors.phone }"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="username"
                            :placeholder="t({ ru: 'your@email.com или +77012345678', kz: 'your@email.com немесе +77012345678' })"
                        />
                        <!-- Hidden inputs for actual form submission -->
                        <input type="hidden" name="email" />
                        <input type="hidden" name="phone" />
                        <InputError :message="errors.email || errors.phone" class="mt-2" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="font-display font-semibold text-steel-800">
                                {{ t({ ru: 'Пароль', kz: 'Құпия сөз' }) }}
                            </label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="font-body text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors duration-200"
                                :tabindex="5"
                            >
                                {{ t({ ru: 'Забыли?', kz: 'Ұмыттыңыз ба?' }) }}
                            </TextLink>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            :class="{ 'border-rust-500 focus:border-rust-500 focus:ring-rust-500/20': errors.password }"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            :placeholder="t({ ru: 'Введите пароль', kz: 'Құпия сөзді енгізіңіз' })"
                        />
                        <InputError :message="errors.password" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <label for="remember" class="font-body text-sm text-steel-700 cursor-pointer">
                            {{ t({ ru: 'Запомнить меня', kz: 'Мені есте сақта' }) }}
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl transition-all duration-200 hover:bg-amber-600 hover:shadow-industrial-lg hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                    >
                        <Spinner v-if="processing" class="w-5 h-5" />
                        <span v-if="!processing">{{ t({ ru: 'Войти', kz: 'Кіру' }) }}</span>
                        <span v-else>{{ t({ ru: 'Вход...', kz: 'Кіру...' }) }}</span>
                    </button>

                    <!-- Sign Up Link -->
                    <div class="text-center pt-4 border-t border-concrete-200" v-if="canRegister">
                        <span class="font-body text-concrete-600">{{ t({ ru: 'Впервые здесь?', kz: 'Бұл жерде бірінші рет пе?' }) }}</span>
                        <TextLink
                            :href="register()"
                            :tabindex="5"
                            class="font-body font-semibold text-amber-600 hover:text-amber-700 transition-colors duration-200 ml-2"
                        >
                            {{ t({ ru: 'Создать аккаунт', kz: 'Аккаунт жасау' }) }}
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
