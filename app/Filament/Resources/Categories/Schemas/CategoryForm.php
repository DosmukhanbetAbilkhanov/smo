<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Concerns\HasBilingualFields;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    use HasBilingualFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...self::bilingualTextInputs('name', 'Category Name (Russian)', 'Category Name (Kazakh)'),

                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name_ru')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                FileUpload::make('icon')
                    ->label('Category Icon')
                    ->image()
                    ->maxSize(1024)
                    ->nullable(),

                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Auto-generated from Russian name'),
            ]);
    }
}
