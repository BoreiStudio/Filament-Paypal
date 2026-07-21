<?php

namespace BoreiStudio\FilamentPayPal\Features\Dashboard\Widgets;

use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PayPalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $week = now()->startOfWeek();
        $month = now()->startOfMonth();

        $todayTotal = Payment::where('status', 'COMPLETED')
            ->where('captured_at', '>=', $today)
            ->sum('amount');

        $weekTotal = Payment::where('status', 'COMPLETED')
            ->where('captured_at', '>=', $week)
            ->sum('amount');

        $monthTotal = Payment::where('status', 'COMPLETED')
            ->where('captured_at', '>=', $month)
            ->sum('amount');

        $todayCount = Payment::where('status', 'COMPLETED')
            ->where('captured_at', '>=', $today)
            ->count();

        return [
            Stat::make(__('filament-paypal::messages.dashboard.today'), number_format($todayTotal, 2) . ' USD')
                ->description(__('filament-paypal::messages.dashboard.today_count', ['count' => $todayCount]))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make(__('filament-paypal::messages.dashboard.this_week'), number_format($weekTotal, 2) . ' USD')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),
            Stat::make(__('filament-paypal::messages.dashboard.this_month'), number_format($monthTotal, 2) . ' USD')
                ->descriptionIcon('heroicon o-chart-bar')
                ->color('info'),
        ];
    }
}
