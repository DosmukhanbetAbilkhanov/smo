import { useLocaleStore } from '@/stores/locale';
import type { Locale } from '@/types/api';
import { storeToRefs } from 'pinia';
import { computed } from 'vue';

/**
 * Locale Composable
 * Provides convenient access to locale functionality
 */
export function useLocale() {
    const localeStore = useLocaleStore();
    const { currentLocale, isRussian, isKazakh } = storeToRefs(localeStore);

    /**
     * Set the application locale
     */
    const setLocale = (locale: Locale) => {
        localeStore.setLocale(locale);
    };

    /**
     * Toggle between ru and kz locales
     */
    const toggleLocale = () => {
        localeStore.toggleLocale();
    };

    /**
     * Get localized value from an object with ru/kz properties
     */
    const getLocalizedValue = <T extends Record<string, unknown>>(
        obj: T,
        key: string,
    ): unknown => {
        return localeStore.getLocalizedValue(obj, key);
    };

    /**
     * Get localized name (common pattern for name_ru/name_kz)
     */
    const getLocalizedName = (obj: {
        name_ru: string;
        name_kz: string;
    }): string => {
        return localeStore.getLocalizedName(obj);
    };

    /**
     * Get localized description (common pattern for description_ru/description_kz)
     */
    const getLocalizedDescription = (obj: {
        description_ru?: string;
        description_kz?: string;
    }): string | undefined => {
        return localeStore.getLocalizedDescription(obj);
    };

    /**
     * Get locale display name for UI
     */
    const localeDisplayName = computed(() => {
        return currentLocale.value === 'ru' ? 'Русский' : 'Қазақша';
    });

    /**
     * Get opposite locale (for toggle button)
     */
    const oppositeLocale = computed(() => {
        return currentLocale.value === 'ru' ? 'kz' : 'ru';
    });

    /**
     * Get opposite locale display name (for toggle button)
     */
    const oppositeLocaleDisplayName = computed(() => {
        return currentLocale.value === 'ru' ? 'Қазақша' : 'Русский';
    });

    /**
     * Reactive translation helper
     * Usage: t({ ru: 'Привет', kz: 'Сәлем' })
     */
    const t = (translations: { ru: string; kz: string }): string => {
        return currentLocale.value === 'ru' ? translations.ru : translations.kz;
    };

    return {
        // State
        currentLocale,
        isRussian,
        isKazakh,
        localeDisplayName,
        oppositeLocale,
        oppositeLocaleDisplayName,
        // Actions
        setLocale,
        toggleLocale,
        getLocalizedValue,
        getLocalizedName,
        getLocalizedDescription,
        t,
    };
}
