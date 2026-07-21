<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Orders\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
