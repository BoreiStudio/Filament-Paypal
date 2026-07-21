<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Orders\Actions\CaptureOrderAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Actions\SyncOrderFromApiAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Resources\OrderResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-paypal::messages.orders.details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('paypal_order_id')
                            ->label(__('filament-paypal::messages.orders.paypal_order_id'))
                            ->copyable(),
                        TextEntry::make('status')
                            ->label(__('filament-paypal::messages.orders.status'))
                            ->badge(),
                        TextEntry::make('intent')
                            ->label(__('filament-paypal::messages.orders.intent')),
                        TextEntry::make('amount')
                            ->label(__('filament-paypal::messages.orders.amount'))
                            ->money(fn ($record) => $record->currency_code),
                        TextEntry::make('currency_code')
                            ->label(__('filament-paypal::messages.orders.currency')),
                        TextEntry::make('payer_email')
                            ->label(__('filament-paypal::messages.orders.payer_email')),
                        TextEntry::make('payer_name')
                            ->label(__('filament-paypal::messages.orders.payer_name')),
                        TextEntry::make('external_reference')
                            ->label(__('filament-paypal::messages.orders.external_reference')),
                        TextEntry::make('description')
                            ->label(__('filament-paypal::messages.orders.description')),
                        TextEntry::make('created_at')
                            ->label(__('filament-paypal::messages.orders.created_at'))
                            ->dateTime(),
                        TextEntry::make('captured_at')
                            ->label(__('filament-paypal::messages.orders.captured_at'))
                            ->dateTime(),
                        TextEntry::make('approval_url')
                            ->label(__('filament-paypal::messages.orders.approval_url'))
                            ->state(fn ($record) => $record->getApprovalUrl())
                            ->copyable()
                            ->copyMessage(__('filament-paypal::messages.orders.link_copied'))
                            ->visible(fn ($record) => ! empty($record->getApprovalUrl()))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_payment_link')
                ->label(__('filament-paypal::messages.orders.open_payment_link'))
                ->icon('heroicon-o-link')
                ->color('info')
                ->visible(fn ($record) => ! empty($record->getApprovalUrl()))
                ->url(fn ($record) => $record->getApprovalUrl(), shouldOpenInNewTab: true),
            Action::make('copy_payment_link')
                ->label(__('filament-paypal::messages.orders.copy_payment_link'))
                ->icon('heroicon-o-clipboard')
                ->color('gray')
                ->visible(fn ($record) => ! empty($record->getApprovalUrl()))
                ->alpineClickHandler(fn ($record) => 'navigator.clipboard.writeText(\''.addslashes($record->getApprovalUrl()).'\')')
                ->action(function ($record) {
                    Notification::make()
                        ->title(__('filament-paypal::messages.orders.link_copied'))
                        ->success()
                        ->send();
                }),
            Action::make('capture')
                ->label(__('filament-paypal::messages.orders.capture'))
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->visible(fn ($record) => $record->isApproved())
                ->action(function ($record) {
                    app(CaptureOrderAction::class)->execute($record);
                    Notification::make()->success()->title(__('filament-paypal::messages.orders.captured'))->send();
                }),
            Action::make('sync')
                ->label(__('filament-paypal::messages.orders.sync'))
                ->icon(Heroicon::ArrowPath)
                ->action(function ($record) {
                    app(SyncOrderFromApiAction::class)->execute($record->paypal_order_id, $record->account_id);
                    Notification::make()->success()->title(__('filament-paypal::messages.orders.synced'))->send();
                }),
        ];
    }
}
