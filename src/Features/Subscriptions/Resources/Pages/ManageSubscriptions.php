<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\Pages;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions\CancelSubscriptionAction;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions\CreateSubscriptionAction;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Plan;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\SubscriptionResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageSubscriptions extends ManageRecords
{
    protected static string $resource = SubscriptionResource::class;

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    Action::make('copy_approval_link')
                        ->label(__('filament-paypal::messages.subscriptions.copy_approval_link'))
                        ->icon(Heroicon::Link)
                        ->color('info')
                        ->visible(fn ($record) => $record->isApprovalPending() && $record->getApprovalUrl())
                        ->alpineClickHandler(fn ($record) => 'navigator.clipboard.writeText(\'' . addslashes($record->getApprovalUrl()) . '\')')
                        ->action(function ($record) {
                            Notification::make()->success()->title(__('filament-paypal::messages.subscriptions.link_copied'))->send();
                        }),
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
                ]),
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label(__('filament-paypal::messages.subscriptions.create'))
                    ->icon(Heroicon::Plus)
                    ->slideOver()
                    ->form([
                        Select::make('plan_id')
                            ->label(__('filament-paypal::messages.subscriptions.plan'))
                            ->options(
                                Plan::with('product')->get()->mapWithKeys(fn ($plan) => [
                                    $plan->id => $plan->name . ' (' . $plan->product->name . ') - ' . $plan->amount . ' ' . $plan->currency_code . ' / ' . $plan->billing_frequency,
                                ])
                            )
                            ->required()
                            ->searchable(),
                        TextInput::make('email_address')
                            ->label(__('filament-paypal::messages.subscriptions.subscriber_email'))
                            ->email()
                            ->required(),
                        TextInput::make('given_name')
                            ->label(__('filament-paypal::messages.subscriptions.given_name')),
                        TextInput::make('surname')
                            ->label(__('filament-paypal::messages.subscriptions.surname')),
                    ])
                    ->action(function (array $data) {
                        $plan = Plan::with('product')->findOrFail($data['plan_id']);

                        $subscription = app(CreateSubscriptionAction::class)->execute($plan, $data);

                        if ($url = $subscription->getApprovalUrl()) {
                            Notification::make()
                                ->success()
                                ->title(__('filament-paypal::messages.subscriptions.created'))
                                ->body(__('filament-paypal::messages.subscriptions.approval_url_hint'))
                                ->send();
                        } else {
                            Notification::make()
                                ->success()
                                ->title(__('filament-paypal::messages.subscriptions.created'))
                                ->send();
                        }
                    }),
            ]);
    }
}
