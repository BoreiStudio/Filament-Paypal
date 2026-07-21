<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Resources;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Features\Orders\Resources\Pages\ListOrders;
use BoreiStudio\FilamentPayPal\Features\Orders\Resources\Pages\ManageOrders;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $cluster = PayPalCluster::class;

    protected static ?string $slug = 'orders';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ShoppingCart;

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => ManageOrders::route('/'),
            'list' => ListOrders::route('/list'),
        ];
    }
}
