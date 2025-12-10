import type { Locale } from '@/types/api';
import { useCookie } from '@vueuse/core';
import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';

/**
 * Locale Store
 * Manages application locale/language switching
 */
export const useLocaleStore = defineStore('locale', () => {
    // Use VueUse's useCookie for reactive cookie management
    const localeCookie = useCookie<Locale>('locale', {
        default: () => 'ru',
        maxAge: 60 * 60 * 24 * 365, // 1 year
    });

    // State - initialize from cookie or localStorage
    const currentLocale = ref<Locale>(
        localeCookie.value ||
            (localStorage.getItem('locale') as Locale | null) ||
            'ru',
    );

    // Computed
    const isRussian = computed(() => currentLocale.value === 'ru');
    const isKazakh = computed(() => currentLocale.value === 'kz');

    // Watch for locale changes and sync to cookie and localStorage
    watch(
        currentLocale,
        (newLocale) => {
            localeCookie.value = newLocale;
            localStorage.setItem('locale', newLocale);
            // Update HTML lang attribute for accessibility
            document.documentElement.lang = newLocale;
        },
        { immediate: true },
    );

    // Actions
    function setLocale(locale: Locale) {
        if (locale !== currentLocale.value) {
            currentLocale.value = locale;
            // Reload page to apply locale changes across the app
            // In a real app, you might want to use i18n plugin instead
            window.location.reload();
        }
    }

    function toggleLocale() {
        const newLocale = currentLocale.value === 'ru' ? 'kz' : 'ru';
        setLocale(newLocale);
    }

    /**
     * Get localized value from an object with ru/kz properties
     */
    function getLocalizedValue<T extends Record<string, unknown>>(
        obj: T,
        key: string,
    ): unknown {
        const localizedKey = `${key}_${currentLocale.value}`;
        return obj[localizedKey] ?? obj[key] ?? '';
    }

    /**
     * Get localized name (common pattern)
     */
    function getLocalizedName(obj: {
        name_ru: string;
        name_kz: string;
    }): string {
        return currentLocale.value === 'ru' ? obj.name_ru : obj.name_kz;
    }

    /**
     * Get localized description (common pattern)
     */
    function getLocalizedDescription(obj: {
        description_ru?: string;
        description_kz?: string;
    }): string | undefined {
        return currentLocale.value === 'ru'
            ? obj.description_ru
            : obj.description_kz;
    }

    return {
        // State
        currentLocale,
        // Computed
        isRussian,
        isKazakh,
        // Actions
        setLocale,
        toggleLocale,
        getLocalizedValue,
        getLocalizedName,
        getLocalizedDescription,
    };
});
