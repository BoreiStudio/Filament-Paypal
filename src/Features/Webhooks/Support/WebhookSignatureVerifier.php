<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Support;

use BoreiStudio\FilamentPayPal\Contracts\CredentialResolverInterface;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class WebhookSignatureVerifier
{
    public function __construct(
        private readonly PayPalClient $client,
        private readonly CredentialResolverInterface $credentialResolver,
    ) {}

    public function verify(array $headers, array $payload): ?bool
    {
        if (! config('paypal.webhooks.verify_signature', true)) {
            return null;
        }

        $credentials = $this->credentialResolver->applicationCredentials();
        $webhookId = $credentials['webhook_id'] ?? null;

        if (! $webhookId) {
            return null;
        }

        try {
            $verificationPayload = [
                'auth_algo' => $headers['PAYPAL-AUTH-ALGO'] ?? '',
                'cert_url' => $headers['PAYPAL-CERT-URL'] ?? '',
                'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
                'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
                'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
                'webhook_id' => $webhookId,
                'webhook_event' => $payload,
            ];

            return $this->client->verifyWebhookSignature($verificationPayload);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function verificationStatus(array $headers): string
    {
        return $headers['PAYPAL-AUTH-ALGO']
            ?? $headers['paypal-auth-algo']
            ?? '';
    }
}
