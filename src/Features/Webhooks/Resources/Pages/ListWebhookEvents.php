<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\WebhookEventResource;
use Filament\Resources\Pages\ListRecords;

class ListWebhookEvents extends ListRecords
{
    protected static string $resource = WebhookEventResource::class;
}
