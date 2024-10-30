<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\BorrowStatus;
use App\Models\BorrowedEquipment;
use App\Models\Equipment;
use App\Models\MissingEquipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SummaryOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Equipment Available', Equipment::where('quantity_available', '>', 0)->count())
                ->description('Per type of equipment')
                ->color('success'),
            Stat::make('Active Borrow Log', BorrowedEquipment::whereNot('status',  BorrowStatus::RETURNED->value)->count())
                ->description('Not returned equipment')
                ->color('warning'),
            Stat::make('Missing Equipment', MissingEquipment::whereNot('is_condemned')->count())
                ->description('Equipment still not found')
                ->color('danger'),
        ];
    }
}
