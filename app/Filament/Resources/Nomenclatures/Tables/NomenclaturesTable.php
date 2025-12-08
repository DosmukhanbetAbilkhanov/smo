<?php

namespace App\Filament\Resources\Nomenclatures\Tables;

use App\Enums\NomenclatureStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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

                TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('M d, Y H:i')
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
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'draft' => 'Draft',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('View'),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === NomenclatureStatus::Pending)
                    ->action(function ($record) {
                        $record->update([
                            'status' => NomenclatureStatus::Approved,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === NomenclatureStatus::Pending)
                    ->action(function ($record) {
                        $record->update([
                            'status' => NomenclatureStatus::Rejected,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn ($record) => $record->update([
                                'status' => NomenclatureStatus::Approved,
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]));
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('reject_selected')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn ($record) => $record->update([
                                'status' => NomenclatureStatus::Rejected,
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]));
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
