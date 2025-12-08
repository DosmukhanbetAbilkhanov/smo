<?php

namespace App\Filament\Seller\Resources\Nomenclatures\Tables;

use App\Enums\NomenclatureStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NomenclaturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ru')
                    ->label('Name (RU)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name_ru')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit.name_ru')
                    ->label('Unit')
                    ->searchable(),

                TextColumn::make('brandname')
                    ->label('Brand')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color())
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->label('Decision Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => in_array($record->status, [NomenclatureStatus::Draft, NomenclatureStatus::Pending])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
