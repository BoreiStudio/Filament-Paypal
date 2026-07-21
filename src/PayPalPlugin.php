<?php

namespace BoreiStudio\FilamentPayPal;

use BoreiStudio\FilamentPayPal\Concerns\HasFeatureToggles;
use Filament\Contracts\Plugin;
use Filament\Panel;

class PayPalPlugin implements Plugin
{
    use HasFeatureToggles;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-paypal';
    }

    public function register(Panel $panel): void
    {
        if ($this->isTenancyEnabled()) {
            config(['paypal.mode' => 'multi_tenant']);
        }

        $this->registerFeatures($panel);
    }

    public function boot(Panel $panel): void {}
}
