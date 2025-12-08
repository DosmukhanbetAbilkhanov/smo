<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('type_id')
                            ->label('User Type')
                            ->relationship('type', 'name_ru')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('city_id')
                            ->label('City')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(3),

                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Account Settings')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Toggle::make('active')
                            ->label('Active Account')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
