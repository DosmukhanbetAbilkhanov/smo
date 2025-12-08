<?php

namespace App\Filament\Seller\Resources\Shops\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')
                    ->default(fn () => Filament::getTenant()->id)
                    ->required(),

                TextInput::make('name')
                    ->label('Shop Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('address')
                    ->label('Shop Address')
                    ->required()
                    ->rows(3)
                    ->maxLength(500),

                Select::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('min_order_amount')
                    ->label('Minimum Order Amount')
                    ->numeric()
                    ->prefix('₸')
                    ->default(0)
                    ->required(),
            ]);
    }
}
