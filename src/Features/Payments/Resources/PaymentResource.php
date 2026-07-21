<?php

namespace BoreiStudio\FilamentPayPal\Features\Payments\Resources;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use BoreiStudio\FilamentPayPal\Features\Payments\Resources\Pages\ListPayments;
use BoreiStudio\FilamentPayPal\Features\Payments\Resources\Pages\ManagePayments;
use BoreiStudio\FilamentPayPal\Features\Payments\Resources\Pages\ViewPayment;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $cluster = PayPalCluster::class;

    protected static ?string $slug = 'payments';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => ManagePayments::route('/'),
            'list' => ListPayments::route('/list'),
            'view' => ViewPayment::route('/{record}'),
        ];
    }
}
