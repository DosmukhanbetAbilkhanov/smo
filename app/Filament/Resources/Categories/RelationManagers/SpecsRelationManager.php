<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecsRelationManager extends RelationManager
{
    protected static string $relationship = 'specs';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_required')
                    ->label('Required')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name_ru')
            ->columns([
                TextColumn::make('name_ru')
                    ->label('Name (Russian)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_kz')
                    ->label('Name (Kazakh)')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Toggle::make('is_required')
                            ->label('Required')
                            ->default(false),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
