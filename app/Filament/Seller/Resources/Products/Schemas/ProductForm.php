<?php

namespace App\Filament\Seller\Resources\Products\Schemas;

use App\Enums\NomenclatureStatus;
use App\Models\Nomenclature;
use App\Models\Shop;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Selection')
                    ->description('Select shop and approved nomenclature')
                    ->schema([
                        Select::make('shop_id')
                            ->label('Shop')
                            ->options(function () {
                                $company = Filament::getTenant();

                                return Shop::where('company_id', $company->id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('nomenclature_id')
                            ->label('Nomenclature')
                            ->options(
                                Nomenclature::where('status', NomenclatureStatus::Approved)
                                    ->get()
                                    ->mapWithKeys(fn ($n) => [$n->id => "{$n->name_ru} ({$n->category->name_ru})"])
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state) {
                                    $nomenclature = Nomenclature::find($state);
                                    if ($nomenclature) {
                                        $set('name_ru', $nomenclature->name_ru);
                                        $set('name_kz', $nomenclature->name_kz);

                                        $requiredSpecs = $nomenclature->category
                                            ->specs()
                                            ->wherePivot('is_required', true)
                                            ->get();

                                        if ($requiredSpecs->isNotEmpty() && empty($get('specs'))) {
                                            $specsData = $requiredSpecs->map(fn ($spec) => [
                                                'spec_name' => $spec->name_ru,
                                                'spec_value' => '',
                                            ])->toArray();

                                            $set('specs', $specsData);
                                        }
                                    }
                                }
                            })
                            ->helperText('Only approved nomenclatures are available')
                            ->columnSpanFull(),
                    ])->columns(1),

                Section::make('Product Details')
                    ->description('Product names and pricing')
                    ->schema([
                        TextInput::make('name_ru')
                            ->label('Name (Russian)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name_kz')
                            ->label('Name (Kazakh)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->prefix('₸')
                            ->minValue(0)
                            ->step(0.01),

                        TextInput::make('quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])->columns(2),

                Section::make('Product Images')
                    ->description('Upload product images (max 5 images)')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Images')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('products')
                            ->visibility('private')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])->columns(1),

                Section::make('Product Specifications')
                    ->description('Add product specifications and technical details')
                    ->schema([
                        Repeater::make('specs')
                            ->label('Specifications')
                            ->relationship('specs')
                            ->schema([
                                TextInput::make('spec_name')
                                    ->label('Specification Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('spec_value')
                                    ->label('Value')
                                    ->required()
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['spec_name'] ?? null)
                            ->columnSpanFull(),
                    ])->columns(1)
                    ->collapsible(),
            ]);
    }
}
