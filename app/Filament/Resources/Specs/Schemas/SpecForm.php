<?php

namespace App\Filament\Resources\Specs\Schemas;

use App\Filament\Concerns\HasBilingualFields;
use Filament\Schemas\Schema;

class SpecForm
{
    use HasBilingualFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...self::bilingualTextInputs('name', 'Specification Name (Russian)', 'Specification Name (Kazakh)'),
            ]);
    }
}
