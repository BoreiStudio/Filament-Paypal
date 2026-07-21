<?php

namespace BoreiStudio\FilamentPayPal;

use BoreiStudio\FilamentPayPal\Console\Commands\WebhookSimulateCommand;
use BoreiStudio\FilamentPayPal\Contracts\CredentialResolverInterface;
use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use BoreiStudio\FilamentPayPal\Features\Payouts\Models\Payout;
use BoreiStudio\FilamentPayPal\Features\Refunds\Models\Refund;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Product;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Models\WebhookEvent;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use BoreiStudio\FilamentPayPal\Policies\PaypalAccountPolicy;
use BoreiStudio\FilamentPayPal\Policies\PaypalPaymentPolicy;
use BoreiStudio\FilamentPayPal\Policies\PaypalPayoutPolicy;
use BoreiStudio\FilamentPayPal\Policies\PaypalProductPolicy;
use BoreiStudio\FilamentPayPal\Policies\PaypalRefundPolicy;
use BoreiStudio\FilamentPayPal\Policies\PaypalSubscriptionPolicy;
use BoreiStudio\FilamentPayPal\Policies\PaypalWebhookEventPolicy;
use BoreiStudio\FilamentPayPal\Support\Credentials\MultiTenantCredentialResolver;
use BoreiStudio\FilamentPayPal\Support\Credentials\SingleTenantCredentialResolver;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PayPalServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-paypal')
            ->hasConfigFile('paypal')
            ->hasTranslations()
            ->hasViews()
            ->hasRoutes('web')
            ->hasMigrations([
                '2026_07_21_000001_create_paypal_accounts_table',
                '2026_07_21_000002_create_paypal_settings',
                '2026_07_21_000003_create_paypal_products_table',
                '2026_07_21_000004_create_paypal_plans_table',
                '2026_07_21_000005_create_paypal_subscriptions_table',
                '2026_07_21_000006_create_paypal_orders_table',
                '2026_07_21_000007_create_paypal_payments_table',
                '2026_07_21_000008_create_paypal_refunds_table',
                '2026_07_21_000009_create_paypal_webhook_events_table',
                '2026_07_21_000010_create_paypal_payouts_table',
                '2026_07_21_000011_add_approval_url_to_paypal_orders',
                '2026_07_21_000012_backfill_approval_url_on_existing_orders',
                '2026_07_21_000013_rename_columns_on_paypal_accounts',
                '2026_07_21_000014_add_separate_webhook_ids_to_paypal_accounts',
                '2026_07_21_000015_make_account_id_nullable_on_webhook_events',
                '2026_07_21_000016_add_approval_url_to_paypal_subscriptions',
                '2026_07_21_000017_make_paypal_subscriptions_plan_id_nullable',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CredentialResolverInterface::class, function ($app) {
            $mode = config('paypal.mode', 'single_tenant');

            return $mode === 'multi_tenant'
                ? new MultiTenantCredentialResolver
                : new SingleTenantCredentialResolver;
        });
    }

    public function packageBooted(): void
    {
        Gate::policy(PaypalAccount::class, PaypalAccountPolicy::class);
        Gate::policy(Payment::class, PaypalPaymentPolicy::class);
        Gate::policy(Refund::class, PaypalRefundPolicy::class);
        Gate::policy(WebhookEvent::class, PaypalWebhookEventPolicy::class);
        Gate::policy(Product::class, PaypalProductPolicy::class);
        Gate::policy(Subscription::class, PaypalSubscriptionPolicy::class);
        Gate::policy(Payout::class, PaypalPayoutPolicy::class);
        Gate::policy(Order::class, PaypalPaymentPolicy::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                WebhookSimulateCommand::class,
            ]);
        }
    }
}
