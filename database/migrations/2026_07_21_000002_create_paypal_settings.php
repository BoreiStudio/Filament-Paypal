<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('paypal-app.client_id', '');
        $this->migrator->add('paypal-app.client_secret', '');
        $this->migrator->add('paypal-app.webhook_id', '');
        $this->migrator->add('paypal-app.sandbox_mode', true);
    }
};
