<?php

declare(strict_types=1);

namespace App\Support\Helpers;

use Illuminate\Support\Facades\App;

class LocalizedAttribute
{
    /**
     * Get a localized attribute value from a model
     *
     * This helper retrieves the correct localized field value based on the current app locale.
     * For example, if the current locale is 'ru', it will return the value of 'name_ru'.
     *
     * @param  object  $model  The model instance
     * @param  string  $attribute  The base attribute name (e.g., 'name', 'description')
     * @param  string|null  $locale  Optional locale override
     */
    public static function get(object $model, string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?? App::getLocale();
        $localizedField = "{$attribute}_{$locale}";

        if (property_exists($model, $localizedField) || isset($model->$localizedField)) {
            return $model->$localizedField;
        }

        // Fallback to Russian if the localized field doesn't exist
        $fallbackField = "{$attribute}_ru";
        if (property_exists($model, $fallbackField) || isset($model->$fallbackField)) {
            return $model->$fallbackField;
        }

        // If no localized field exists, try the base attribute
        if (property_exists($model, $attribute) || isset($model->$attribute)) {
            return $model->$attribute;
        }

        return null;
    }

    /**
     * Get all localized attributes for a given base attribute
     *
     * Returns an array with all available localized versions of an attribute.
     *
     * @param  object  $model  The model instance
     * @param  string  $attribute  The base attribute name
     * @return array<string, string|null>
     */
    public static function getAll(object $model, string $attribute): array
    {
        $locales = config('app.available_locales', ['ru', 'kz', 'en']);
        $result = [];

        foreach ($locales as $locale) {
            $localizedField = "{$attribute}_{$locale}";
            $result[$locale] = property_exists($model, $localizedField) || isset($model->$localizedField)
                ? $model->$localizedField
                : null;
        }

        return $result;
    }

    /**
     * Check if a model has a localized attribute
     *
     * @param  object  $model  The model instance
     * @param  string  $attribute  The base attribute name
     * @param  string|null  $locale  Optional locale to check
     */
    public static function has(object $model, string $attribute, ?string $locale = null): bool
    {
        if ($locale) {
            $localizedField = "{$attribute}_{$locale}";

            return property_exists($model, $localizedField) || isset($model->$localizedField);
        }

        // Check if any localized version exists
        $locales = config('app.available_locales', ['ru', 'kz', 'en']);

        foreach ($locales as $loc) {
            $localizedField = "{$attribute}_{$loc}";
            if (property_exists($model, $localizedField) || isset($model->$localizedField)) {
                return true;
            }
        }

        return false;
    }
}
