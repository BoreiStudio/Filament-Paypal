<?php

namespace BoreiStudio\FilamentPayPal\Features\Payments\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Payments\Actions\SyncPaymentFromApiAction;
use BoreiStudio\FilamentPayPal\Features\Payments\Resources\PaymentResource;
use BoreiStudio\FilamentPayPal\Features\Refunds\Actions\CreateRefundAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-paypal::messages.payments.details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('paypal_capture_id')
                            ->label(__('filament-paypal::messages.payments.paypal_capture_id')),
                        TextEntry::make('order.paypal_order_id')
                            ->label(__('filament-paypal::messages.payments.order')),
                        TextEntry::make('status')
                            ->label(__('filament-paypal::messages.payments.status'))
                            ->badge(),
                        TextEntry::make('amount')
                            ->label(__('filament-paypal::messages.payments.amount'))
                            ->money(fn ($record) => $record->currency_code),
                        TextEntry::make('payment_method')
                            ->label(__('filament-paypal::messages.payments.payment_method')),
                        TextEntry::make('payer_email')
                            ->label(__('filament-paypal::messages.payments.payer_email')),
                        TextEntry::make('external_reference')
                            ->label(__('filament-paypal::messages.payments.external_reference')),
                        TextEntry::make('captured_at')
                            ->label(__('filament-paypal::messages.payments.captured_at'))
                            ->dateTime(),
                    ]),
                Section::make(__('filament-paypal::messages.refunds.title'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('refunded_amount')
                            ->label(__('filament-paypal::messages.payments.refunded_amount'))
                            ->money(fn ($record) => $record->currency_code)
                            ->default('0.00'),
                        TextEntry::make('available_for_refund')
                            ->label(__('filament-paypal::messages.payments.available_for_refund'))
                            ->money(fn ($record) => $record->currency_code)
                            ->default('0.00'),
                    ]),
                Section::make(__('filament-paypal::messages.payments.paypal_response'))
                    ->schema([
                        TextEntry::make('paypal_response')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                            ->monospace()
                            ->visible(fn ($state) => ! empty($state)),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refund')
                ->label(__('filament-paypal::messages.refunds.create'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->visible(fn ($record) => $record->isCompleted() && $record->getAvailableForRefund() > 0)
                ->form([
                    TextInput::make('amount')
                        ->label(__('filament-paypal::messages.refunds.amount'))
                        ->numeric()
                        ->required()
                        ->minValue(0.01)
                        ->maxValue(fn ($record) => $record->getAvailableForRefund()),
                    TextInput::make('note_to_payer')
                        ->label(__('filament-paypal::messages.refunds.note')),
                ])
                ->action(function ($record, array $data) {
                    app(CreateRefundAction::class)->execute($record, $data);
                    Notification::make()->success()->title(__('filament-paypal::messages.refunds.created'))->send();
                }),
            Action::make('sync')
                ->label(__('filament-paypal::messages.payments.sync'))
                ->icon('heroicon-o-arrow-path')
                ->action(function ($record) {
                    app(SyncPaymentFromApiAction::class)->execute($record->paypal_capture_id);
                    Notification::make()->success()->title(__('filament-paypal::messages.payments.synced'))->send();
                }),
        ];
    }
}
