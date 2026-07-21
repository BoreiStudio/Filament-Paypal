<?php

namespace BoreiStudio\FilamentPayPal\Features\Refunds\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Refunds\Resources\RefundResource;
use Filament\Resources\Pages\ListRecords;

class ListRefunds extends ListRecords
{
    protected static string $resource = RefundResource::class;
}
