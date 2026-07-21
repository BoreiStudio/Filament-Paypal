<?php

namespace BoreiStudio\FilamentPayPal\Clusters;

use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class PayPalConfigCluster extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::Cog6Tooth;

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function getNavigationLabel(): string
    {
        return __('filament-paypal::messages.cluster.config');
    }

    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();
        $account = PaypalAccount::query()
            ->when($tenant, fn ($q) => $q->byTenant($tenant))
            ->where('status', 'connected')
            ->first();

        if (! $account) {
            return '!';
        }

        return '✓';
    }

    public static function setNavigationGroup(string | \UnitEnum | null $group): void
    {
        static::$navigationGroup = $group;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $tenant = Filament::getTenant();
        $account = PaypalAccount::query()
            ->when($tenant, fn ($q) => $q->byTenant($tenant))
            ->where('status', 'connected')
            ->first();

        return $account ? 'success' : 'danger';
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
