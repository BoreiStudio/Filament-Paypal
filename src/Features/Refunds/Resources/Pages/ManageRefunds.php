<?php

namespace BoreiStudio\FilamentPayPal\Features\Refunds\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Refunds\Resources\RefundResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ManageRefunds extends ManageRecords
{
    protected static string $resource = RefundResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('paypal_refund_id')
                    ->label(__('filament-paypal::messages.refunds.paypal_refund_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment.paypal_capture_id')
                    ->label(__('filament-paypal::messages.refunds.payment')),
                TextColumn::make('status')
                    ->label(__('filament-paypal::messages.refunds.status'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('filament-paypal::messages.refunds.amount'))
                    ->money(fn ($record) => $record->payment?->currency_code ?? 'USD')
                    ->sortable(),
                TextColumn::make('note_to_payer')
                    ->label(__('filament-paypal::messages.refunds.note'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament-paypal::messages.refunds.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'COMPLETED' => 'COMPLETED',
                        'PENDING' => 'PENDING',
                        'FAILED' => 'FAILED',
                        'CANCELLED' => 'CANCELLED',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->icon(Heroicon::Eye)
                        ->slideOver()
                        ->modalHeading(__('filament-paypal::messages.refunds.view'))
                        ->fillForm(fn ($record) => [
                            'paypal_refund_id' => $record->paypal_refund_id,
                            'status' => $record->status->getLabel(),
                            'amount' => number_format($record->amount, 2).' '.($record->payment?->currency_code ?? 'USD'),
                            'note_to_payer' => $record->note_to_payer,
                            'payment_id' => $record->payment?->paypal_capture_id,
                            'created_at' => $record->created_at?->format('M d, Y H:i:s'),
                        ])
                        ->schema([
                            Section::make(__('filament-paypal::messages.refunds.title'))
                                ->columns(2)
                                ->schema([
                                    TextInput::make('paypal_refund_id')
                                        ->label(__('filament-paypal::messages.refunds.paypal_refund_id'))
                                        ->disabled(),
                                    TextInput::make('status')
                                        ->label(__('filament-paypal::messages.refunds.status'))
                                        ->disabled(),
                                    TextInput::make('amount')
                                        ->label(__('filament-paypal::messages.refunds.amount'))
                                        ->disabled(),
                                    TextInput::make('payment_id')
                                        ->label(__('filament-paypal::messages.refunds.payment'))
                                        ->disabled(),
                                    TextInput::make('note_to_payer')
                                        ->label(__('filament-paypal::messages.refunds.note'))
                                        ->disabled(),
                                    TextInput::make('created_at')
                                        ->label(__('filament-paypal::messages.refunds.created_at'))
                                        ->disabled(),
                                ]),
                        ]),
                ]),
            ]);
    }
}
