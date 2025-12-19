<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { Package, Shield } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const { t } = useLocale();
const mounted = ref(false);

onMounted(() => {
    mounted.value = true;
});
</script>

<template>
    <Head :title="t({ ru: 'Подтверждение пароля', kz: 'Құпия сөзді растау' })" />

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

            <!-- Confirm Password Card -->
            <div class="bg-white rounded-3xl shadow-industrial-xl border border-concrete-200 p-8 sm:p-10">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-50 rounded-xl text-amber-600">
                            <Shield :size="24" :stroke-width="2.5" />
                        </div>
                        <h1 class="font-display text-3xl font-bold text-steel-900">
                            {{ t({ ru: 'Защищенная область', kz: 'Қорғалған аймақ' }) }}
                        </h1>
                    </div>
                    <p class="font-body text-concrete-600">
                        {{ t({ ru: 'Это защищенная область приложения. Пожалуйста, подтвердите пароль для продолжения.', kz: 'Бұл қолданбаның қорғалған аймағы. Жалғастыру үшін құпия сөзді растаңыз.' }) }}
                    </p>
                </div>

                <!-- Form -->
                <Form
                    v-bind="store.form()"
                    reset-on-success
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <!-- Password Field -->
                    <div>
                        <label htmlFor="password" class="font-display font-semibold text-steel-800 mb-2 block">
                            {{ t({ ru: 'Пароль', kz: 'Құпия сөз' }) }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
                            :class="{ 'border-rust-500 focus:border-rust-500 focus:ring-rust-500/20': errors.password }"
                            required
                            autocomplete="current-password"
                            autofocus
                            :placeholder="t({ ru: 'Введите пароль', kz: 'Құпия сөзді енгізіңіз' })"
                        />
                        <InputError :message="errors.password" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl transition-all duration-200 hover:bg-amber-600 hover:shadow-industrial-lg hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" class="w-5 h-5" />
                        <span v-if="!processing">{{ t({ ru: 'Подтвердить', kz: 'Растау' }) }}</span>
                        <span v-else>{{ t({ ru: 'Подтверждение...', kz: 'Расталуда...' }) }}</span>
                    </button>
                </Form>
            </div>

            <!-- Footer Text -->
            <p class="font-body text-center text-sm text-concrete-500 mt-6">
                {{ t({ ru: 'Защищено шифрованием', kz: 'Шифрлаумен қорғалған' }) }}
            </p>
        </div>
    </div>
</template>
