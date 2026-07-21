<?php

namespace BoreiStudio\FilamentPayPal\Features\Refunds\Resources;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Refunds\Models\Refund;
use BoreiStudio\FilamentPayPal\Features\Refunds\Resources\Pages\ListRefunds;
use BoreiStudio\FilamentPayPal\Features\Refunds\Resources\Pages\ManageRefunds;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class RefundResource extends Resource
{
    protected static ?string $model = Refund::class;

    protected static ?string $cluster = PayPalCluster::class;

    protected static ?string $slug = 'refunds';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ArrowUturnLeft;

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRefunds::route('/'),
            'list' => ListRefunds::route('/list'),
        ];
    }
}
