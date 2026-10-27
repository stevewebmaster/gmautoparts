<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\ReservationStatus;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PartResource;
use App\Filament\Resources\ReservationResource;
use App\Filament\Resources\VehicleResource;
use App\Models\Order;
use App\Models\Part;
use App\Models\Reservation;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The state of the yard at a glance, on the admin landing page.
 *
 * Every card links through to the list it counts, so the number is a way in to
 * the work rather than just a number.
 */
class BusinessOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $toPack = Order::where('status', OrderStatus::Paid)->count();
        $holding = Reservation::holding()->count();

        return [
            Stat::make('Orders to pack', (string) $toPack)
                ->description($toPack === 0 ? 'Nothing waiting' : 'Paid for, not yet sent')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($toPack > 0 ? 'success' : 'gray')
                ->url(OrderResource::getUrl('index', [
                    'tableFilters' => ['status' => ['values' => [OrderStatus::Paid->value]]],
                ])),

            Stat::make('Parts on hold', (string) $holding)
                ->description($holding === 0 ? 'No current holds' : 'Reserved for a customer')
                ->descriptionIcon('heroicon-m-clock')
                ->color($holding > 0 ? 'warning' : 'gray')
                ->url(ReservationResource::getUrl('index', [
                    'tableFilters' => ['status' => ['values' => ReservationStatus::holding()]],
                ])),

            Stat::make('Parts on the site', (string) Part::query()->listable()->count())
                ->description('Available or on hold')
                ->descriptionIcon('heroicon-m-cog')
                ->color('gray')
                ->url(PartResource::getUrl('index')),

            Stat::make('Vehicles dismantling', (string) Vehicle::where('is_visible', true)->count())
                ->description('Showing in Now Dismantling')
                ->descriptionIcon('heroicon-m-truck')
                ->color('gray')
                ->url(VehicleResource::getUrl('index')),
        ];
    }
}
