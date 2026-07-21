<?php

namespace BoreiStudio\FilamentPayPal\Features\Payments\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Payments\Actions\SyncPaymentFromApiAction;
use BoreiStudio\FilamentPayPal\Features\Payments\Resources\PaymentResource;
use BoreiStudio\FilamentPayPal\Features\Refunds\Actions\CreateRefundAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ManagePayments extends ManageRecords
{
    protected static string $resource = PaymentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('captured_at', 'desc')
            ->columns([
                TextColumn::make('paypal_capture_id')
                    ->label(__('filament-paypal::messages.payments.paypal_capture_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label(__('filament-paypal::messages.payments.status'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('filament-paypal::messages.payments.amount'))
                    ->money(fn ($record) => $record->currency_code)
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(__('filament-paypal::messages.payments.payment_method')),
                TextColumn::make('payer_email')
                    ->label(__('filament-paypal::messages.payments.payer_email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('external_reference')
                    ->label(__('filament-paypal::messages.payments.external_reference'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('captured_at')
                    ->label(__('filament-paypal::messages.payments.captured_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'COMPLETED' => 'Completed',
                        'PENDING' => 'Pending',
                        'REFUNDED' => 'Refunded',
                        'PARTIALLY_REFUNDED' => 'Partially Refunded',
                        'DECLINED' => 'Declined',
                        'FAILED' => 'Failed',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->icon(Heroicon::Eye)
                        ->slideOver()
                        ->modalHeading(__('filament-paypal::messages.payments.details'))
                        ->fillForm(fn ($record) => [
                            'paypal_capture_id' => $record->paypal_capture_id,
                            'status' => $record->status->getLabel(),
                            'amount' => number_format($record->amount, 2).' '.$record->currency_code,
                            'payment_method' => $record->payment_method,
                            'payer_email' => $record->payer_email,
                            'external_reference' => $record->external_reference,
                            'captured_at' => $record->captured_at?->format('M d, Y H:i:s'),
                            'order_id' => $record->order?->paypal_order_id,
                            'refunded' => number_format($record->getRefundedAmount(), 2).' '.$record->currency_code,
                            'available' => number_format($record->getAvailableForRefund(), 2).' '.$record->currency_code,
                        ])
                        ->schema([
                            Section::make('Payment')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('paypal_capture_id')
                                        ->label(__('filament-paypal::messages.payments.paypal_capture_id'))
                                        ->disabled(),
                                    TextInput::make('status')
                                        ->label(__('filament-paypal::messages.payments.status'))
                                        ->disabled(),
                                    TextInput::make('amount')
                                        ->label(__('filament-paypal::messages.payments.amount'))
                                        ->disabled(),
                                    TextInput::make('payment_method')
                                        ->label(__('filament-paypal::messages.payments.payment_method'))
                                        ->disabled(),
                                    TextInput::make('captured_at')
                                        ->label(__('filament-paypal::messages.payments.captured_at'))
                                        ->disabled(),
                                    TextInput::make('order_id')
                                        ->label(__('filament-paypal::messages.payments.order'))
                                        ->disabled(),
                                ]),
                            Section::make('Buyer')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('payer_email')
                                        ->label(__('filament-paypal::messages.payments.payer_email'))
                                        ->disabled(),
                                    TextInput::make('external_reference')
                                        ->label(__('filament-paypal::messages.payments.external_reference'))
                                        ->disabled(),
                                ]),
                            Section::make(__('filament-paypal::messages.refunds.title'))
                                ->columns(2)
                                ->schema([
                                    TextInput::make('refunded')
                                        ->label(__('filament-paypal::messages.payments.refunded_amount'))
                                        ->disabled(),
                                    TextInput::make('available')
                                        ->label(__('filament-paypal::messages.payments.available_for_refund'))
                                        ->disabled(),
                                ]),
                        ]),
                    Action::make('refund')
                        ->label(__('filament-paypal::messages.refunds.create'))
                        ->icon(Heroicon::ArrowUturnLeft)
                        ->color('warning')
                        ->slideOver()
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
                        ->icon(Heroicon::ArrowPath)
                        ->action(function ($record) {
                            app(SyncPaymentFromApiAction::class)->execute($record->paypal_capture_id);
                            Notification::make()->success()->title(__('filament-paypal::messages.payments.synced'))->send();
                        }),
                ]),
            ]);
    }
}
