<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions\CancelSubscriptionAction;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\Pages\ManageSubscriptions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $cluster = PayPalCluster::class;

    protected static ?string $slug = 'subscriptions';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ArrowPath;

    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('paypal_subscription_id')
                    ->label(__('filament-paypal::messages.subscriptions.paypal_subscription_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('plan.name')
                    ->label(__('filament-paypal::messages.subscriptions.plan')),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('subscriber_email')
                    ->label(__('filament-paypal::messages.subscriptions.subscriber_email'))
                    ->searchable(),
                TextColumn::make('next_billing_time')
                    ->label(__('filament-paypal::messages.subscriptions.next_billing'))
                    ->dateTime(),
                TextColumn::make('last_payment_amount')
                    ->label(__('filament-paypal::messages.subscriptions.last_payment_amount'))
                    ->money('USD'),
                TextColumn::make('failed_payments_count')
                    ->label(__('filament-paypal::messages.subscriptions.failed_payments')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'APPROVAL_PENDING' => 'Approval Pending',
                        'APPROVED' => 'Approved',
                        'ACTIVE' => 'Active',
                        'SUSPENDED' => 'Suspended',
                        'CANCELLED' => 'Cancelled',
                        'EXPIRED' => 'Expired',
                    ]),
            ])
            ->recordActions([
                Action::make('cancel')
                    ->label(__('filament-paypal::messages.subscriptions.cancel'))
                    ->icon(Heroicon::XCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->isActive())
                    ->action(function ($record) {
                        app(CancelSubscriptionAction::class)->execute($record);
                        Notification::make()->warning()->title(__('filament-paypal::messages.subscriptions.cancelled'))->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubscriptions::route('/'),
        ];
    }
}
