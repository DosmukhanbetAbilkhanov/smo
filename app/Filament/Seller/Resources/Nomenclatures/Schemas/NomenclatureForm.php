<?php

namespace App\Filament\Seller\Resources\Nomenclatures\Schemas;

use App\Models\Category;
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
                Section::make('Product Names')
                    ->description('Provide bilingual product names')
                    ->schema([
                        TextInput::make('name_ru')
                            ->label('Name (Russian)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name_kz')
                            ->label('Name (Kazakh)')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Category & Unit')
                    ->description('Select category and measurement unit')
                    ->schema([
                        Select::make('parent_category_id')
                            ->label('Parent Category')
                            ->options(Category::whereNull('parent_id')->pluck('name_ru', 'id'))
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
                            ->label('Sub-Category')
                            ->options(function (callable $get) {
                                $parentId = $get('parent_category_id');
                                if (! $parentId) {
                                    return [];
                                }

                                // Only show leaf categories (categories with no children)
                                return Category::where('parent_id', $parentId)
                                    ->whereDoesntHave('children')
                                    ->pluck('name_ru', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->disabled(fn (callable $get) => ! $get('parent_category_id'))
                            ->helperText('Select a parent category first. Only leaf categories are available.'),

                        Select::make('unit_id')
                            ->label('Unit of Measurement')
                            ->relationship('unit', 'name_ru')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Descriptions')
                    ->description('Optional detailed descriptions')
                    ->schema([
                        Textarea::make('description_ru')
                            ->label('Description (Russian)')
                            ->rows(4)
                            ->maxLength(1000),

                        Textarea::make('description_kz')
                            ->label('Description (Kazakh)')
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Product Identifiers')
                    ->description('Optional product codes and brand information')
                    ->schema([
                        TextInput::make('brandname')
                            ->label('Brand Name')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('SKU')
                            ->label('SKU (Stock Keeping Unit)')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('GTIN')
                            ->label('GTIN (Global Trade Item Number)')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('NTIN')
                            ->label('NTIN (National Trade Item Number)')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
