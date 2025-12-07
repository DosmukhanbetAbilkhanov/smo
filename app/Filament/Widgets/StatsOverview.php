<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('Products in the marketplace')
                ->color('success'),

            Stat::make('Total Sellers', User::where('type_id', 4)->count())
                ->description('Active seller accounts')
                ->color('warning'),

            Stat::make('Total Categories', Category::count())
                ->description('Product categories')
                ->color('primary'),
        ];
    }
}
