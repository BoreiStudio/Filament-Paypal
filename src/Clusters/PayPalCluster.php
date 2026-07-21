<?php

namespace BoreiStudio\FilamentPayPal\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class PayPalCluster extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::CreditCard;

    protected static ?int $navigationSort = 1;

    protected static string | \UnitEnum | null $navigationGroup = 'PayPal';

    public static function getNavigationLabel(): string
    {
        return __('filament-paypal::messages.cluster.paypal');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public static function setNavigationGroup(string | \UnitEnum | null $group): void
    {
        static::$navigationGroup = $group;
    }
}
