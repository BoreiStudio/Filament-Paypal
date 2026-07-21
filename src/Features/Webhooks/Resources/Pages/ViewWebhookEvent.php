<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\WebhookEventResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewWebhookEvent extends ViewRecord
{
    protected static string $resource = WebhookEventResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-paypal::messages.webhooks.details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('paypal_event_id')
                            ->label(__('filament-paypal::messages.webhooks.paypal_event_id')),
                        TextEntry::make('event_type')
                            ->label(__('filament-paypal::messages.webhooks.event_type'))
                            ->badge(),
                        TextEntry::make('resource_type')
                            ->label(__('filament-paypal::messages.webhooks.resource_type')),
                        TextEntry::make('resource_id')
                            ->label(__('filament-paypal::messages.webhooks.resource_id')),
                        TextEntry::make('summary')
                            ->label(__('filament-paypal::messages.webhooks.summary')),
                        TextEntry::make('signature_valid')
                            ->label(__('filament-paypal::messages.webhooks.signature_valid'))
                            ->formatStateUsing(fn ($state) => $state ? '✓' : '✗'),
                        TextEntry::make('status')
                            ->label(__('filament-paypal::messages.webhooks.status'))
                            ->badge(),
                        TextEntry::make('error')
                            ->label(__('filament-paypal::messages.webhooks.error')),
                        TextEntry::make('created_at')
                            ->label(__('filament-paypal::messages.webhooks.received'))
                            ->dateTime(),
                        TextEntry::make('processed_at')
                            ->label(__('filament-paypal::messages.webhooks.processed'))
                            ->dateTime(),
                    ]),
                Section::make(__('filament-paypal::messages.webhooks.raw_payload'))
                    ->schema([
                        TextEntry::make('raw_payload')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                            ->monospace(),
                    ]),
            ]);
    }
}
