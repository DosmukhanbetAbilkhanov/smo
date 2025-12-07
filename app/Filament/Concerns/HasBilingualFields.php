<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\TextInput;

trait HasBilingualFields
{
    protected static function bilingualTextInputs(string $fieldName, ?string $labelRu = null, ?string $labelKz = null, bool $required = true): array
    {
        return [
            TextInput::make("{$fieldName}_ru")
                ->label($labelRu ?? ucfirst($fieldName).' (RU)')
                ->required($required)
                ->maxLength(255),

            TextInput::make("{$fieldName}_kz")
                ->label($labelKz ?? ucfirst($fieldName).' (KZ)')
                ->required($required)
                ->maxLength(255),
        ];
    }
}
