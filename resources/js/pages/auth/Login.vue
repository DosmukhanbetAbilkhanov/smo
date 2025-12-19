<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

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
    <Head title="Log in">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    </Head>

    <div class="warm-login-container">
        <!-- Background Elements -->
        <div class="warm-bg-gradient"></div>
        <div class="warm-blob warm-blob-1"></div>
        <div class="warm-blob warm-blob-2"></div>
        <div class="warm-grain"></div>

        <!-- Main Content -->
        <div class="warm-content" :class="{ 'warm-mounted': mounted }">
            <!-- Logo/Brand Area -->
            <!-- <div class="warm-brand">
                <div class="warm-logo-circle">
                    <svg class="warm-logo-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div> -->

            <!-- Card -->
            <div class="warm-card">
                <div class="warm-card-inner">
                    <!-- Header -->
                    <div class="warm-header">
                        <h1 class="warm-title">Welcome Back</h1>
                        <p class="warm-subtitle">Sign in to continue to your account</p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status" class="warm-status">
                        <svg class="warm-status-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ status }}
                    </div>

                    <!-- Form -->
                    <Form
                        v-bind="store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="warm-form"
                    >
                        <!-- Email/Phone Field -->
                        <div class="warm-field">
                            <label for="identifier" class="warm-label">Email or Phone</label>
                            <div class="warm-input-wrapper">
                                <input
                                    id="identifier"
                                    v-model="identifier"
                                    type="text"
                                    class="input-modern w-full"
                                    :class="{ 'warm-input-error': errors.email || errors.phone }"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="username"
                                    placeholder="your@email.com or +77012345678"
                                />
                                <div class="warm-input-border"></div>
                            </div>
                            <!-- Hidden inputs for actual form submission -->
                            <input type="hidden" name="email" />
                            <input type="hidden" name="phone" />
                            <InputError :message="errors.email || errors.phone" class="warm-error" />
                        </div>

                        <!-- Password Field -->
                        <div class="warm-field">
                            <div class="warm-label-row">
                                <label for="password" class="warm-label">Password</label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="warm-link-small"
                                    :tabindex="5"
                                >
                                    Forgot?
                                </TextLink>
                            </div>
                            <div class="warm-input-wrapper">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="input-modern w-full"
                                    :class="{ 'warm-input-error': errors.password }"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                />
                                <div class="warm-input-border"></div>
                            </div>
                            <InputError :message="errors.password" class="warm-error" />
                        </div>

                        <!-- Remember Me -->
                        <div class="warm-remember">
                            <label for="remember" class="warm-checkbox-label">
                                <Checkbox id="remember" name="remember" :tabindex="3" class="warm-checkbox" />
                                <span>Keep me signed in</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            class="btn-primary-modern"
                            :tabindex="4"
                            :disabled="processing"
                            data-test="login-button"
                        >
                            <Spinner v-if="processing" class="warm-spinner" />
                            <span v-if="!processing">Sign In</span>
                            <span v-else>Signing in...</span>
                        </Button>

                        <!-- Sign Up Link -->
                        <div class="warm-footer" v-if="canRegister">
                            <span class="warm-footer-text">New here?</span>
                            <TextLink :href="register()" :tabindex="5" class="warm-link">
                                Create an account
                            </TextLink>
                        </div>
                    </Form>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="warm-decorative-line"></div>
        </div>
    </div>
</template>

<style scoped>
/* ========================================
   WARM MODERN LOGIN - DESIGN SYSTEM
   ======================================== */

/* Typography */
.warm-login-container {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
}

.warm-title,
.warm-brand {
    font-family: 'Crimson Pro', Georgia, serif;
}

/* Color Variables */
:root {
    --warm-terracotta: #D4735E;
    --warm-burnt-orange: #C86850;
    --warm-sienna: #E8825C;
    --warm-cream: #FFF8F0;
    --warm-sand: #F5E6D3;
    --warm-espresso: #5C3D2E;
    --warm-warm-gray: #8B7355;
    --warm-light-peach: #FFE8DC;
    --warm-deep-clay: #B35942;
}

/* ========================================
   CONTAINER & BACKGROUND
   ======================================== */

.warm-login-container {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 2rem;
    background: var(--warm-cream);
}

.warm-bg-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        var(--warm-cream) 0%,
        var(--warm-sand) 40%,
        var(--warm-light-peach) 100%
    );
    z-index: 0;
}

/* Organic Blob Shapes */
.warm-blob {
    position: absolute;
    border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
    opacity: 0.15;
    filter: blur(60px);
    animation: warm-blob-float 20s ease-in-out infinite;
}

.warm-blob-1 {
    width: 500px;
    height: 500px;
    background: var(--warm-terracotta);
    top: -10%;
    left: -10%;
    animation-delay: 0s;
}

.warm-blob-2 {
    width: 400px;
    height: 400px;
    background: var(--warm-sienna);
    bottom: -10%;
    right: -10%;
    animation-delay: -10s;
}

@keyframes warm-blob-float {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg) scale(1);
    }
    33% {
        transform: translate(30px, -50px) rotate(120deg) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) rotate(240deg) scale(0.9);
    }
}

/* Grain Texture */
.warm-grain {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
    opacity: 0.03;
    z-index: 1;
    pointer-events: none;
}

/* ========================================
   CONTENT & ANIMATIONS
   ======================================== */

.warm-content {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 440px;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.warm-content.warm-mounted {
    opacity: 1;
    transform: translateY(0);
}

/* ========================================
   BRAND/LOGO
   ======================================== */

.warm-brand {
    display: flex;
    justify-content: center;
    margin-bottom: 2.5rem;
    animation: warm-fade-slide-down 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.2s backwards;
}

.warm-logo-circle {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--warm-terracotta), var(--warm-sienna));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(212, 115, 94, 0.25);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.warm-logo-circle:hover {
    transform: scale(1.05) rotate(-5deg);
}

.warm-logo-icon {
    width: 32px;
    height: 32px;
    color: white;
}

@keyframes warm-fade-slide-down {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
}

/* ========================================
   CARD
   ======================================== */

.warm-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow:
        0 0 0 1px rgba(92, 61, 46, 0.04),
        0 20px 60px rgba(92, 61, 46, 0.08);
    overflow: hidden;
    animation: warm-fade-slide-up 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.3s backwards;
}

.warm-card-inner {
    padding: 2.5rem;
}

@media (max-width: 640px) {
    .warm-card-inner {
        padding: 2rem 1.5rem;
    }
}

@keyframes warm-fade-slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
}

/* ========================================
   HEADER
   ======================================== */

.warm-header {
    text-align: center;
    margin-bottom: 2rem;
}

.warm-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--warm-espresso);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.warm-subtitle {
    font-size: 0.9375rem;
    color: var(--warm-warm-gray);
    font-weight: 500;
}

/* ========================================
   STATUS MESSAGE
   ======================================== */

.warm-status {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #D4F4DD, #C8E6D0);
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #2D5F3F;
    margin-bottom: 1.5rem;
    animation: warm-status-appear 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.warm-status-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

@keyframes warm-status-appear {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
}

/* ========================================
   FORM
   ======================================== */

.warm-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ========================================
   FORM FIELDS
   ======================================== */

.warm-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.warm-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--warm-espresso);
    letter-spacing: 0.01em;
}

.warm-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.warm-input-wrapper {
    position: relative;
}

.warm-input {
    width: 100%;
    padding: 0.875rem 1rem;
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--warm-espresso);
    background: white;
    border: 2px solid rgba(92, 61, 46, 0.1);
    border-radius: 12px;
    outline: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.warm-input::placeholder {
    color: var(--warm-warm-gray);
    opacity: 0.5;
}

.warm-input:focus {
    border-color: var(--warm-terracotta);
    box-shadow: 0 0 0 4px rgba(212, 115, 94, 0.1);
    transform: translateY(-1px);
}

.warm-input-error {
    border-color: #DC2626;
}

.warm-input-error:focus {
    box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
}

.warm-input-border {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--warm-terracotta), var(--warm-sienna));
    transform: scaleX(0);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0 0 12px 12px;
}

.warm-input:focus + .warm-input-border {
    transform: scaleX(1);
}

/* ========================================
   ERROR MESSAGES
   ======================================== */

.warm-error {
    font-size: 0.8125rem;
    color: #DC2626;
    font-weight: 500;
}

/* ========================================
   LINKS
   ======================================== */

.warm-link-small {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--warm-terracotta);
    text-decoration: none;
    transition: all 0.2s;
    position: relative;
}

.warm-link-small::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--warm-terracotta);
    transform: scaleX(0);
    transition: transform 0.2s;
}

.warm-link-small:hover::after {
    transform: scaleX(1);
}

.warm-link {
    font-weight: 600;
    color: var(--warm-terracotta);
    text-decoration: none;
    position: relative;
    transition: color 0.2s;
}

.warm-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--warm-terracotta);
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.warm-link:hover {
    color: var(--warm-burnt-orange);
}

.warm-link:hover::after {
    transform: scaleX(1);
    transform-origin: left;
}

/* ========================================
   REMEMBER ME
   ======================================== */

.warm-remember {
    display: flex;
    align-items: center;
}

.warm-checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--warm-espresso);
    cursor: pointer;
    user-select: none;
}

/* ========================================
   SUBMIT BUTTON
   ======================================== */

.warm-submit {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, var(--warm-terracotta), var(--warm-burnt-orange));
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(212, 115, 94, 0.3);
    position: relative;
    overflow: hidden;
}

.warm-submit::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--warm-sienna), var(--warm-deep-clay));
    opacity: 0;
    transition: opacity 0.3s;
}

.warm-submit:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(212, 115, 94, 0.4);
}

.warm-submit:hover:not(:disabled)::before {
    opacity: 1;
}

.warm-submit:active:not(:disabled) {
    transform: translateY(0);
}

.warm-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.warm-submit > * {
    position: relative;
    z-index: 1;
}

.warm-spinner {
    display: inline-block;
    margin-right: 0.5rem;
}

/* ========================================
   FOOTER
   ======================================== */

.warm-footer {
    text-align: center;
    font-size: 0.875rem;
    padding-top: 0.5rem;
}

.warm-footer-text {
    color: var(--warm-warm-gray);
    font-weight: 500;
    margin-right: 0.375rem;
}

/* ========================================
   DECORATIVE LINE
   ======================================== */

.warm-decorative-line {
    height: 3px;
    background: linear-gradient(90deg,
        transparent,
        var(--warm-terracotta),
        var(--warm-sienna),
        transparent
    );
    margin-top: 3rem;
    border-radius: 2px;
    opacity: 0.3;
    animation: warm-line-expand 1.2s cubic-bezier(0.4, 0, 0.2, 1) 0.6s backwards;
}

@keyframes warm-line-expand {
    from {
        transform: scaleX(0);
        opacity: 0;
    }
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

@media (max-width: 640px) {
    .warm-login-container {
        padding: 1rem;
    }

    .warm-title {
        font-size: 1.75rem;
    }

    .warm-subtitle {
        font-size: 0.875rem;
    }

    .warm-logo-circle {
        width: 56px;
        height: 56px;
    }

    .warm-logo-icon {
        width: 28px;
        height: 28px;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .warm-bg-gradient {
        background: linear-gradient(
            135deg,
            #2A1F1A 0%,
            #3D2E24 40%,
            #4A3629 100%
        );
    }

    .warm-login-container {
        background: #2A1F1A;
    }

    .warm-card {
        background: rgba(61, 46, 36, 0.7);
    }

    .warm-title {
        color: var(--warm-sand);
    }

    .warm-subtitle {
        color: var(--warm-light-peach);
    }

    .warm-label {
        color: var(--warm-sand);
    }

    .warm-input {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 248, 240, 0.1);
        color: var(--warm-cream);
    }

    .warm-checkbox-label {
        color: var(--warm-sand);
    }

    .warm-footer-text {
        color: var(--warm-light-peach);
    }
}
</style>
