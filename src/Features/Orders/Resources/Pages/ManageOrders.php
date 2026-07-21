<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Orders\Actions\CaptureOrderAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Actions\CreateOrderAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Actions\SyncOrderFromApiAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Resources\OrderResource;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ManageOrders extends ManageRecords
{
    protected static string $resource = OrderResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('paypal_order_id')
                    ->label(__('filament-paypal::messages.orders.paypal_order_id'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('status')
                    ->label(__('filament-paypal::messages.orders.status'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('filament-paypal::messages.orders.amount'))
                    ->money(fn ($record) => $record->currency_code)
                    ->sortable(),
                TextColumn::make('payer_email')
                    ->label(__('filament-paypal::messages.orders.payer_email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('external_reference')
                    ->label(__('filament-paypal::messages.orders.external_reference'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament-paypal::messages.orders.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => \BoreiStudio\FilamentPayPal\Features\Orders\Enums\OrderStatus::class)
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->icon(Heroicon::Eye)
                        ->slideOver()
                        ->modalHeading(fn ($record) => __('filament-paypal::messages.orders.details') . ' - ' . $record->paypal_order_id)
                        ->fillForm(fn ($record) => [
                            'paypal_order_id' => $record->paypal_order_id,
                            'status' => $record->status->getLabel(),
                            'amount' => number_format($record->amount, 2) . ' ' . $record->currency_code,
                            'intent' => $record->intent,
                            'payer_email' => $record->payer_email,
                            'payer_name' => $record->payer_name,
                            'external_reference' => $record->external_reference,
                            'description' => $record->description,
                            'created_at' => $record->created_at?->format('M d, Y H:i:s'),
                            'captured_at' => $record->captured_at?->format('M d, Y H:i:s') ?? '—',
                            'approval_url' => $record->getApprovalUrl(),
                        ])
                        ->schema([
                            Section::make(__('filament-paypal::messages.orders.details'))
                                ->columns(2)
                                ->schema([
                                    TextInput::make('paypal_order_id')
                                        ->label(__('filament-paypal::messages.orders.paypal_order_id'))
                                        ->disabled(),
                                    TextInput::make('status')
                                        ->label(__('filament-paypal::messages.orders.status'))
                                        ->disabled(),
                                    TextInput::make('amount')
                                        ->label(__('filament-paypal::messages.orders.amount'))
                                        ->disabled(),
                                    TextInput::make('intent')
                                        ->label(__('filament-paypal::messages.orders.intent'))
                                        ->disabled(),
                                ]),
                            Section::make('Buyer')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('payer_email')
                                        ->label(__('filament-paypal::messages.orders.payer_email'))
                                        ->disabled(),
                                    TextInput::make('payer_name')
                                        ->label(__('filament-paypal::messages.orders.payer_name'))
                                        ->disabled(),
                                    TextInput::make('external_reference')
                                        ->label(__('filament-paypal::messages.orders.external_reference'))
                                        ->disabled(),
                                    TextInput::make('description')
                                        ->label(__('filament-paypal::messages.orders.description'))
                                        ->disabled(),
                                ]),
                            Section::make('Timestamps')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('created_at')
                                        ->label(__('filament-paypal::messages.orders.created_at'))
                                        ->disabled(),
                                    TextInput::make('captured_at')
                                        ->label(__('filament-paypal::messages.orders.captured_at'))
                                        ->disabled(),
                                ]),
                            Section::make(__('filament-paypal::messages.orders.approval_url'))
                                ->visible(fn ($state) => ! empty($state['approval_url']))
                                ->schema([
                                    TextInput::make('approval_url')
                                        ->label('')
                                        ->disabled(),
                                ]),
                        ]),
                    Action::make('open_payment_link')
                        ->label(__('filament-paypal::messages.orders.open_payment_link'))
                        ->icon(Heroicon::Link)
                        ->color('info')
                        ->visible(fn ($record) => ! empty($record->getApprovalUrl()))
                        ->url(fn ($record) => $record->getApprovalUrl(), shouldOpenInNewTab: true),
                    Action::make('copy_payment_link')
                        ->label(__('filament-paypal::messages.orders.copy_payment_link'))
                        ->icon(Heroicon::Clipboard)
                        ->color('gray')
                        ->visible(fn ($record) => ! empty($record->getApprovalUrl()))
                        ->alpineClickHandler(fn ($record) => 'navigator.clipboard.writeText(\'' . addslashes($record->getApprovalUrl()) . '\')')
                        ->action(function ($record) {
                            Notification::make()->success()->title(__('filament-paypal::messages.orders.link_copied'))->send();
                        }),
                    Action::make('capture')
                        ->label(__('filament-paypal::messages.orders.capture'))
                        ->icon(Heroicon::CreditCard)
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
                ]),
            ])
            ->toolbarActions([
                Action::make('createOrder')
                    ->label(__('filament-paypal::messages.orders.create'))
                    ->icon(Heroicon::Plus)
                    ->slideOver()
                    ->form([
                        Select::make('account_id')
                            ->label(__('filament-paypal::messages.orders.account'))
                            ->options(PaypalAccount::where('status', 'connected')->pluck('production_client_id', 'id'))
                            ->required(),
                        Select::make('currency_code')
                            ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'])
                            ->default('USD')
                            ->required(),
                        TextInput::make('description')
                            ->label(__('filament-paypal::messages.orders.description')),
                        TextInput::make('external_reference')
                            ->label(__('filament-paypal::messages.orders.external_reference')),
                        Repeater::make('items')
                            ->label(__('filament-paypal::messages.orders.items'))
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('amount')->numeric()->required(),
                                TextInput::make('quantity')->numeric()->default(1),
                            ])
                            ->defaultItems(1),
                    ])
                    ->action(function (array $data) {
                        app(CreateOrderAction::class)->execute($data);
                        Notification::make()->success()->title(__('filament-paypal::messages.orders.created'))->send();
                    }),
            ]);
    }
}
