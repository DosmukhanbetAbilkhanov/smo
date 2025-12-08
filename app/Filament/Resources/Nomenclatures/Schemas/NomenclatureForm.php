<?php

namespace App\Filament\Resources\Nomenclatures\Schemas;

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
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name_kz')
                            ->label('Name (Kazakh)')
                            ->required()
                            ->maxLength(255),

                        Select::make('unit_id')
                            ->label('Unit')
                            ->relationship('unit', 'name_ru')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('parent_category_id')
                            ->label('Parent Category')
                            ->options(\App\Models\Category::whereNull('parent_id')->pluck('name_ru', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('category_id', null))
                            ->dehydrated(false)
                            ->default(function ($record) {
                                if ($record && $record->category_id) {
                                    return $record->category?->parent_id;
                                }

                                return null;
                            }),

                        Select::make('category_id')
                            ->label('Child Category')
                            ->options(function (callable $get) {
                                $parentId = $get('parent_category_id');
                                if (! $parentId) {
                                    return [];
                                }

                                return \App\Models\Category::where('parent_id', $parentId)
                                    ->pluck('name_ru', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->disabled(fn (callable $get) => ! $get('parent_category_id'))
                            ->helperText('Select a parent category first'),
                    ])
                    ->columns(2),

                Section::make('Descriptions')
                    ->schema([
                        Textarea::make('description_ru')
                            ->label('Description (Russian)')
                            ->rows(3)
                            ->maxLength(1000),

                        Textarea::make('description_kz')
                            ->label('Description (Kazakh)')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Section::make('Product Identifiers')
                    ->schema([
                        TextInput::make('SKU')
                            ->label('SKU')
                            ->maxLength(255),

                        TextInput::make('GTIN')
                            ->label('GTIN')
                            ->maxLength(255),

                        TextInput::make('NTIN')
                            ->label('NTIN')
                            ->maxLength(255),

                        TextInput::make('brandname')
                            ->label('Brand Name')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Approval Status')
                    ->schema([
                        TextInput::make('status_display')
                            ->label('Current Status')
                            ->default(fn ($record) => $record?->status?->label() ?? 'Pending Review')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('approved_by_display')
                            ->label('Approved By')
                            ->default(fn ($record) => $record?->approvedBy?->name ?? '-')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('approved_at_display')
                            ->label('Approved At')
                            ->default(fn ($record) => $record?->approved_at?->format('M d, Y H:i') ?? '-')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3)
                    ->visible(fn ($context) => $context === 'edit'),
            ]);
    }
}
