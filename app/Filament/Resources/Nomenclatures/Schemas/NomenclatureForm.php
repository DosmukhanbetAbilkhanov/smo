<?php

namespace App\Filament\Resources\Nomenclatures\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NomenclatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name_ru')
                            ->label('Name (Russian)')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('name_kz')
                            ->label('Name (Kazakh)')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('unit_id')
                            ->label('Unit')
                            ->relationship('unit', 'name_ru')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name_ru')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Descriptions')
                    ->schema([
                        Textarea::make('description_ru')
                            ->label('Description (Russian)')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false),

                        Textarea::make('description_kz')
                            ->label('Description (Kazakh)')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Product Identifiers')
                    ->schema([
                        TextInput::make('SKU')
                            ->label('SKU')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('GTIN')
                            ->label('GTIN')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('NTIN')
                            ->label('NTIN')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('brandname')
                            ->label('Brand Name')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Approval Status')
                    ->schema([
                        Placeholder::make('status')
                            ->label('Current Status')
                            ->content(fn ($record) => $record?->status?->label() ?? 'Pending Review'),

                        Placeholder::make('approved_by')
                            ->label('Approved By')
                            ->content(fn ($record) => $record?->approvedBy?->name ?? '-'),

                        Placeholder::make('approved_at')
                            ->label('Approved At')
                            ->content(fn ($record) => $record?->approved_at?->format('M d, Y H:i') ?? '-'),
                    ])
                    ->columns(3),
            ]);
    }
}
