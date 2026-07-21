<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\WebhookEventResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ManageWebhookEvents extends ManageRecords
{
    protected static string $resource = WebhookEventResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('event_type')
                    ->label(__('filament-paypal::messages.webhooks.event_type'))
                    ->searchable()
                    ->badge(),
                TextColumn::make('resource_id')
                    ->label(__('filament-paypal::messages.webhooks.resource_id'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('signature_valid')
                    ->label(__('filament-paypal::messages.webhooks.signature_valid'))
                    ->boolean(),
                TextColumn::make('status')
                    ->label(__('filament-paypal::messages.webhooks.status'))
                    ->badge(),
                TextColumn::make('error')
                    ->label(__('filament-paypal::messages.webhooks.error'))
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament-paypal::messages.webhooks.received'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->label(__('filament-paypal::messages.webhooks.processed'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processed' => 'Processed',
                        'failed' => 'Failed',
                    ])
                    ->native(false),
                SelectFilter::make('event_type')
                    ->options([
                        'CHECKOUT.ORDER.APPROVED' => 'CHECKOUT.ORDER.APPROVED',
                        'CHECKOUT.ORDER.COMPLETED' => 'CHECKOUT.ORDER.COMPLETED',
                        'PAYMENT.CAPTURE.COMPLETED' => 'PAYMENT.CAPTURE.COMPLETED',
                        'PAYMENT.CAPTURE.REFUNDED' => 'PAYMENT.CAPTURE.REFUNDED',
                        'PAYMENT.CAPTURE.DENIED' => 'PAYMENT.CAPTURE.DENIED',
                    ]),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon(Heroicon::Eye)
                    ->slideOver()
                    ->modalHeading(__('filament-paypal::messages.webhooks.details'))
                    ->fillForm(fn ($record) => [
                        'id' => $record->id,
                        'paypal_event_id' => $record->paypal_event_id,
                        'event_type' => $record->event_type,
                        'resource_type' => $record->resource_type,
                        'resource_id' => $record->resource_id,
                        'summary' => $record->summary,
                        'signature_valid' => $record->signature_valid ? '✓' : '✗',
                        'status' => $record->status->getLabel(),
                        'error' => $record->error,
                        'created_at' => $record->created_at?->format('M d, Y H:i:s'),
                        'processed_at' => $record->processed_at?->format('M d, Y H:i:s'),
                        'raw_payload' => json_encode($record->raw_payload, JSON_PRETTY_PRINT),
                    ])
                    ->schema([
                        Section::make(__('filament-paypal::messages.webhooks.details'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('id')->disabled(),
                                TextInput::make('paypal_event_id')
                                    ->label(__('filament-paypal::messages.webhooks.paypal_event_id'))
                                    ->disabled(),
                                TextInput::make('event_type')
                                    ->label(__('filament-paypal::messages.webhooks.event_type'))
                                    ->disabled(),
                                TextInput::make('resource_type')
                                    ->label(__('filament-paypal::messages.webhooks.resource_type'))
                                    ->disabled(),
                                TextInput::make('resource_id')
                                    ->label(__('filament-paypal::messages.webhooks.resource_id'))
                                    ->disabled(),
                                TextInput::make('summary')
                                    ->label(__('filament-paypal::messages.webhooks.summary'))
                                    ->disabled()
                                    ->columnSpanFull(),
                                TextInput::make('signature_valid')
                                    ->label(__('filament-paypal::messages.webhooks.signature_valid'))
                                    ->disabled(),
                                TextInput::make('status')
                                    ->label(__('filament-paypal::messages.webhooks.status'))
                                    ->disabled(),
                                TextInput::make('error')
                                    ->label(__('filament-paypal::messages.webhooks.error'))
                                    ->disabled()
                                    ->visible(fn ($state) => ! empty($state['error'])),
                                TextInput::make('created_at')
                                    ->label(__('filament-paypal::messages.webhooks.received'))
                                    ->disabled(),
                                TextInput::make('processed_at')
                                    ->label(__('filament-paypal::messages.webhooks.processed'))
                                    ->disabled(),
                            ]),
                        Section::make(__('filament-paypal::messages.webhooks.raw_payload'))
                            ->schema([
                                Textarea::make('raw_payload')
                                    ->label('')
                                    ->disabled()
                                    ->rows(10),
                            ]),
                    ]),
            ]);
    }
}
