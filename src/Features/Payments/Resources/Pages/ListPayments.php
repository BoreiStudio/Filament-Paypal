<?php

namespace BoreiStudio\FilamentPayPal\Features\Payments\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Payments\Resources\PaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;
}
