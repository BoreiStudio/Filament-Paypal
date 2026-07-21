<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Pages;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Orders\Actions\CreateOrderAction;
use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $cluster = PayPalCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament-paypal::table-page';

    public static function getNavigationLabel(): string
    {
        return __('filament-paypal::messages.orders.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query())
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
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('filament-paypal::messages.orders.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'CREATED' => 'CREATED',
                        'APPROVED' => 'APPROVED',
                        'COMPLETED' => 'COMPLETED',
                        'VOIDED' => 'VOIDED',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(__('filament-paypal::messages.orders.view'))
                    ->modalContent(fn ($record) => view('filament-paypal::orders.view-modal', ['order' => $record])),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createOrder')
                ->label(__('filament-paypal::messages.orders.create'))
                ->icon('heroicon-o-plus')
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
                    $order = app(CreateOrderAction::class)->execute($data);
                    Notification::make()->success()->title(__('filament-paypal::messages.orders.created'))->send();
                }),
        ];
    }
}
