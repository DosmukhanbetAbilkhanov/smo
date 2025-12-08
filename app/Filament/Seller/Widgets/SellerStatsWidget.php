<?php

namespace App\Filament\Seller\Widgets;

use App\Enums\NomenclatureStatus;
use App\Models\Nomenclature;
use App\Models\Product;
use App\Models\Shop;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SellerStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $company = Filament::getTenant();

        $shopsCount = Shop::where('company_id', $company->id)->count();

        $productsCount = Product::whereHas('shop', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->count();

        $pendingNomenclaturesCount = Nomenclature::where('status', NomenclatureStatus::Pending)->count();

        return [
            Stat::make('Total Shops', $shopsCount)
                ->description('Shops in your company')
                ->color('success')
                ->icon('heroicon-o-building-storefront'),

            Stat::make('Total Products', $productsCount)
                ->description('Products across all shops')
                ->color('primary')
                ->icon('heroicon-o-cube'),

            Stat::make('Pending Nomenclatures', $pendingNomenclaturesCount)
                ->description('Awaiting admin approval')
                ->color('warning')
                ->icon('heroicon-o-clock'),
        ];
    }
}
