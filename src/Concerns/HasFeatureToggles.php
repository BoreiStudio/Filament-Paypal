<?php

namespace BoreiStudio\FilamentPayPal\Concerns;

use BoreiStudio\FilamentPayPal\Clusters\PayPalCluster;
use BoreiStudio\FilamentPayPal\Features\Dashboard\Widgets\PayPalStatsWidget;
use BoreiStudio\FilamentPayPal\Features\Docs\Pages\DocumentationPage;
use BoreiStudio\FilamentPayPal\Features\Orders\Resources\OrderResource;
use BoreiStudio\FilamentPayPal\Features\Payments\Resources\PaymentResource;
use BoreiStudio\FilamentPayPal\Features\Payouts\Pages\PayoutsPage;
use BoreiStudio\FilamentPayPal\Features\Refunds\Resources\RefundResource;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\PlanResource;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\ProductResource;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Resources\SubscriptionResource;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Resources\WebhookEventResource;
use BoreiStudio\FilamentPayPal\Settings\PayPalSettingsPage;
use Filament\Panel;

trait HasFeatureToggles
{
    protected bool $orders = true;

    protected bool $refunds = true;

    protected bool $subscriptions = false;

    protected bool $payouts = false;

    protected bool $webhooks = true;

    protected bool $dashboard = true;

    protected bool $documentation = true;

    protected bool $tenancy = false;

    protected ?string $settingsRole = null;

    protected ?string $navigationGroup = null;

    public function orders(bool $condition = true): static
    {
        $this->orders = $condition;

        return $this;
    }

    public function refunds(bool $condition = true): static
    {
        $this->refunds = $condition;

        return $this;
    }

    public function subscriptions(bool $condition = true): static
    {
        $this->subscriptions = $condition;

        return $this;
    }

    public function payouts(bool $condition = true): static
    {
        $this->payouts = $condition;

        return $this;
    }

    public function webhooks(bool $condition = true): static
    {
        $this->webhooks = $condition;

        return $this;
    }

    public function dashboard(bool $condition = true): static
    {
        $this->dashboard = $condition;

        return $this;
    }

    public function documentation(bool $condition = true): static
    {
        $this->documentation = $condition;

        return $this;
    }

    public function tenancy(bool $condition = true): static
    {
        $this->tenancy = $condition;

        return $this;
    }

    public function navigationGroup(?string $group = null): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }

    public function isOrdersEnabled(): bool
    {
        return $this->orders;
    }

    public function isRefundsEnabled(): bool
    {
        return $this->refunds;
    }

    public function isSubscriptionsEnabled(): bool
    {
        return $this->subscriptions;
    }

    public function isPayoutsEnabled(): bool
    {
        return $this->payouts;
    }

    public function isWebhooksEnabled(): bool
    {
        return $this->webhooks;
    }

    public function isDashboardEnabled(): bool
    {
        return $this->dashboard;
    }

    public function isDocumentationEnabled(): bool
    {
        return $this->documentation;
    }

    public function isTenancyEnabled(): bool
    {
        return $this->tenancy;
    }

    public function settingsRole(?string $role = null): static
    {
        $this->settingsRole = $role;

        return $this;
    }

    public function getSettingsRole(): ?string
    {
        return $this->settingsRole;
    }

    public function registerFeatures(Panel $panel): void
    {
        $resources = [];
        $pages = [];
        $widgets = [];

        $group = $this->getNavigationGroup();
        if ($group) {
            PayPalCluster::setNavigationGroup($group);
        }

        if ($role = $this->getSettingsRole()) {
            config(['filament-paypal.settings_role' => $role]);
        }

        if ($this->isDashboardEnabled()) {
            $widgets[] = PayPalStatsWidget::class;
        }

        if ($this->isOrdersEnabled()) {
            $resources[] = OrderResource::class;
            $resources[] = PaymentResource::class;
        }

        if ($this->isRefundsEnabled()) {
            $resources[] = RefundResource::class;
        }

        if ($this->isSubscriptionsEnabled()) {
            $resources[] = ProductResource::class;
            $resources[] = PlanResource::class;
            $resources[] = SubscriptionResource::class;
        }

        if ($this->isWebhooksEnabled()) {
            $resources[] = WebhookEventResource::class;
        }

        if ($this->isPayoutsEnabled()) {
            $pages[] = PayoutsPage::class;
        }

        if ($this->isDocumentationEnabled()) {
            $pages[] = DocumentationPage::class;
        }

        $pages[] = PayPalSettingsPage::class;

        $panel
            ->discoverClusters(
                in: __DIR__.'/../Clusters',
                for: 'BoreiStudio\\FilamentPayPal\\Clusters',
            )
            ->resources($resources)
            ->pages($pages)
            ->widgets($widgets);
    }
}
