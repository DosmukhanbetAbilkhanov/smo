<?php

namespace App\Filament\Resources\Units\Schemas;

use App\Filament\Concerns\HasBilingualFields;
use Filament\Schemas\Schema;

class UnitForm
{
    use HasBilingualFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...self::bilingualTextInputs('name', 'Name (Russian)', 'Name (Kazakh)'),
            ]);
    }
}
