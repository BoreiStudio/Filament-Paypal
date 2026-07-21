<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Resources;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Models\WebhookEvent;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\Pages\ListWebhookEvents;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\Pages\ManageWebhookEvents;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\Pages\ViewWebhookEvent;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class WebhookEventResource extends Resource
{
    protected static ?string $model = WebhookEvent::class;

    protected static ?string $cluster = PayPalCluster::class;

    protected static ?string $slug = 'webhook-events';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ArrowPathRoundedSquare;

    protected static ?int $navigationSort = 6;

    public static function getPages(): array
    {
        return [
            'index' => ManageWebhookEvents::route('/'),
            'list' => ListWebhookEvents::route('/list'),
            'view' => ViewWebhookEvent::route('/{record}'),
        ];
    }
}
