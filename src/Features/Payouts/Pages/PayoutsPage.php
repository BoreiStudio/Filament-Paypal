<?php

namespace BoreiStudio\FilamentPayPal\Features\Payouts\Pages;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Payouts\Actions\CreatePayoutAction;
use BoreiStudio\FilamentPayPal\Features\Payouts\Models\Payout;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PayoutsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $cluster = PayPalCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament-paypal::table-page';

    public static function getNavigationLabel(): string
    {
        return __('filament-paypal::messages.payouts.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->query(Payout::query())
            ->columns([
                TextColumn::make('paypal_batch_id')
                    ->label(__('filament-paypal::messages.payouts.batch_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label(__('filament-paypal::messages.payouts.status'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('filament-paypal::messages.payouts.amount'))
                    ->money(fn ($record) => $record->currency_code)
                    ->sortable(),
                TextColumn::make('recipient_value')
                    ->label(__('filament-paypal::messages.payouts.recipient'))
                    ->searchable(),
                TextColumn::make('recipient_name')
                    ->label(__('filament-paypal::messages.payouts.recipient_name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('note')
                    ->label(__('filament-paypal::messages.payouts.note'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament-paypal::messages.payouts.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'SUCCESS' => 'SUCCESS',
                        'DENIED' => 'DENIED',
                        'CANCELLED' => 'CANCELLED',
                        'FAILED' => 'FAILED',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon(Heroicon::Eye)
                    ->slideOver()
                    ->modalHeading(__('filament-paypal::messages.payouts.view'))
                    ->fillForm(fn ($record) => [
                        'paypal_batch_id' => $record->paypal_batch_id,
                        'status' => $record->status->getLabel(),
                        'amount' => number_format($record->amount, 2).' '.$record->currency_code,
                        'recipient_type' => $record->recipient_type,
                        'recipient_value' => $record->recipient_value,
                        'recipient_name' => $record->recipient_name,
                        'note' => $record->note,
                        'email_subject' => $record->email_subject,
                        'created_at' => $record->created_at?->format('M d, Y H:i:s'),
                        'completed_at' => $record->completed_at?->format('M d, Y H:i:s') ?? '—',
                    ])
                    ->schema([
                        Section::make(__('filament-paypal::messages.payouts.title'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('paypal_batch_id')
                                    ->label(__('filament-paypal::messages.payouts.batch_id'))
                                    ->disabled(),
                                TextInput::make('status')
                                    ->label(__('filament-paypal::messages.payouts.status'))
                                    ->disabled(),
                                TextInput::make('amount')
                                    ->label(__('filament-paypal::messages.payouts.amount'))
                                    ->disabled(),
                                TextInput::make('recipient_type')
                                    ->label(__('filament-paypal::messages.payouts.recipient_type'))
                                    ->disabled(),
                                TextInput::make('recipient_value')
                                    ->label(__('filament-paypal::messages.payouts.recipient'))
                                    ->disabled(),
                                TextInput::make('recipient_name')
                                    ->label(__('filament-paypal::messages.payouts.recipient_name'))
                                    ->disabled(),
                                TextInput::make('note')
                                    ->label(__('filament-paypal::messages.payouts.note'))
                                    ->disabled()
                                    ->columnSpanFull(),
                                TextInput::make('email_subject')
                                    ->label(__('filament-paypal::messages.payouts.email_subject'))
                                    ->disabled()
                                    ->columnSpanFull(),
                                TextInput::make('created_at')
                                    ->label(__('filament-paypal::messages.payouts.created_at'))
                                    ->disabled(),
                                TextInput::make('completed_at')
                                    ->label(__('filament-paypal::messages.payouts.completed_at'))
                                    ->disabled(),
                            ]),
                    ]),
            ])
            ->toolbarActions([
                Action::make('createPayout')
                    ->label(__('filament-paypal::messages.payouts.create'))
                    ->icon(Heroicon::Plus)
                    ->slideOver()
                    ->form([
                        Select::make('account_id')
                            ->label(__('filament-paypal::messages.payouts.account'))
                            ->options(PaypalAccount::where('status', 'connected')->pluck('production_client_id', 'id'))
                            ->required(),
                        Select::make('recipient_type')
                            ->options(['EMAIL' => 'Email', 'PHONE' => 'Phone', 'PAYPAL_ID' => 'PayPal ID'])
                            ->default('EMAIL'),
                        TextInput::make('recipient_value')
                            ->label(__('filament-paypal::messages.payouts.recipient_value'))
                            ->required(),
                        TextInput::make('recipient_name')
                            ->label(__('filament-paypal::messages.payouts.recipient_name')),
                        TextInput::make('amount')
                            ->label(__('filament-paypal::messages.payouts.amount'))
                            ->numeric()
                            ->required()
                            ->minValue(0.01),
                        Select::make('currency_code')
                            ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'])
                            ->default('USD'),
                        TextInput::make('note')
                            ->label(__('filament-paypal::messages.payouts.note')),
                        TextInput::make('email_subject')
                            ->label(__('filament-paypal::messages.payouts.email_subject')),
                    ])
                    ->action(function (array $data) {
                        if ($data['recipient_type'] === 'EMAIL' && ! filter_var($data['recipient_value'], FILTER_VALIDATE_EMAIL)) {
                            Notification::make()->danger()->title(__('filament-paypal::messages.payouts.error'))->body(__('filament-paypal::messages.payouts.invalid_email'))->send();

                            return;
                        }
                        try {
                            app(CreatePayoutAction::class)->execute($data);
                            Notification::make()->success()->title(__('filament-paypal::messages.payouts.created'))->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title(__('filament-paypal::messages.payouts.error'))->body($e->getMessage())->send();
                        }
                    }),
            ]);
    }
}
