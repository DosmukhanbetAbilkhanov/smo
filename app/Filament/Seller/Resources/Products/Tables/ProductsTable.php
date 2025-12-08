<?php

namespace App\Filament\Seller\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Image')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->getStateUsing(fn ($record) => $record->images ? (is_array($record->images) ? $record->images : []) : []),

                TextColumn::make('name_ru')
                    ->label('Name (RU)')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('shop.name')
                    ->label('Shop')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nomenclature.name_ru')
                    ->label('Nomenclature')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('nomenclature.category.name_ru')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('KZT')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('specs_count')
                    ->label('Specs')
                    ->counts('specs')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('shop_id')
                    ->label('Shop')
                    ->relationship('shop', 'name'),

                SelectFilter::make('nomenclature_id')
                    ->label('Nomenclature')
                    ->relationship('nomenclature', 'name_ru')
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
