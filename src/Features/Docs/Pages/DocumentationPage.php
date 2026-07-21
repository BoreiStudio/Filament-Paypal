<?php

namespace BoreiStudio\FilamentPayPal\Features\Docs\Pages;

use BoreiStudio\FilamentPayPal\Clusters\PayPalConfigCluster;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class DocumentationPage extends Page
{
    protected static ?string $slug = 'docs';

    protected string $view = 'filament-paypal::docs.page';

    public static function getNavigationSort(): ?int
    {
        return 11;
    }

    public static function getCluster(): ?string
    {
        return PayPalConfigCluster::class;
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        $role = config('filament-paypal.settings_role');

        if ($role && method_exists($user, 'hasRole') && $user->hasRole($role)) {
            return true;
        }

        if (! $role && method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return true;
        }

        return $user->can('viewAny', PaypalAccount::class);
    }

    public string $selectedModule = '16-quick-start';

    public string $content = '';

    public array $modules = [];

    public function mount(): void
    {
        $this->modules = $this->getModules();
        $this->loadContent();
    }

    public function selectModule(string $module): void
    {
        $this->selectedModule = $module;
        $this->loadContent();
    }

    public function loadContent(): void
    {
        $modules = $this->getModules();
        $path = __DIR__ . '/../../../../docs/' . $this->selectedModule . '/index.md';

        if (! isset($modules[$this->selectedModule]) || ! file_exists($path)) {
            $this->content = '<p class="text-gray-400">Document not found.</p>';

            return;
        }

        $this->content = Str::markdown(file_get_contents($path), [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function copyMarkdown(): void
    {
        $path = __DIR__ . '/../../../../docs/' . $this->selectedModule . '/index.md';

        if (! file_exists($path)) {
            return;
        }

        $this->js('navigator.clipboard.writeText(' . json_encode(file_get_contents($path)) . ')');

        Notification::make()
            ->title('Markdown copied to clipboard.')
            ->success()
            ->send();
    }

    public function getModules(): array
    {
        return [
            '16-quick-start' => 'Quick Start',
            '01-installation' => 'Installation & Configuration',
            '02-credentials' => 'Application Credentials',
            '03-orders' => 'Orders',
            '04-payments' => 'Payments',
            '05-refunds' => 'Refunds',
            '06-webhooks' => 'Webhooks',
            '07-subscriptions' => 'Subscriptions',
            '08-payouts' => 'Payouts',
            '09-checkout' => 'Public Checkout',
            '10-dashboard' => 'Dashboard',
            '11-multi-tenant' => 'Multi-Tenant',
            '12-feature-toggles' => 'Feature Toggles',
            '13-translations' => 'Translations',
            '14-testing' => 'Testing',
            '15-api-reference' => 'API Reference',
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return 'Documentation';
    }

    public static function getNavigationLabel(): string
    {
        return 'Documentation';
    }

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return \Filament\Support\Icons\Heroicon::BookOpen;
    }
}
