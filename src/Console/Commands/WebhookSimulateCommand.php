<?php

namespace BoreiStudio\FilamentPayPal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class WebhookSimulateCommand extends Command
{
    protected $signature = 'paypal:webhook-simulate
        {--event-type=PAYMENT.CAPTURE.COMPLETED}
        {--resource-id=TEST001}
        {--url= : Webhook URL to send to}';

    protected $description = 'Simulate a PayPal webhook event for testing';

    public function handle(): int
    {
        $eventType = $this->option('event-type');
        $resourceId = $this->option('resource-id');
        $targetUrl = $this->option('url') ?? url('/paypal/webhooks');

        $payload = [
            'id' => 'WH-'.strtoupper(uniqid()),
            'event_type' => $eventType,
            'resource_type' => 'capture',
            'resource' => [
                'id' => $resourceId,
                'status' => 'COMPLETED',
                'amount' => [
                    'total' => '10.00',
                    'currency' => 'USD',
                ],
            ],
            'summary' => "Simulated: {$eventType} for resource {$resourceId}",
            'create_time' => now()->toIso8601String(),
        ];

        $this->info("Sending webhook event: {$eventType}");
        $this->line("Target URL: {$targetUrl}");
        $this->line('Payload: '.json_encode($payload, JSON_PRETTY_PRINT));

        $response = Http::withHeaders([
            'PAYPAL-AUTH-ALGO' => 'SHA256withRSA',
            'PAYPAL-CERT-URL' => 'https://api.paypal.com/cert',
            'PAYPAL-TRANSMISSION-ID' => (string) str()->uuid(),
            'PAYPAL-TRANSMISSION-SIG' => 'simulated-signature',
            'PAYPAL-TRANSMISSION-TIME' => now()->toIso8601String(),
        ])->post($targetUrl, $payload);

        $this->line("Response: {$response->status()} - {$response->body()}");

        return Command::SUCCESS;
    }
}
