<?php

namespace BoreiStudio\FilamentPayPal\Support\Http;

use BoreiStudio\FilamentPayPal\Contracts\CredentialResolverInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

class PayPalClient
{
    private readonly string $baseUrl;

    private readonly bool $sandbox;

    public function __construct(
        private readonly CredentialResolverInterface $credentialResolver,
    ) {
        $credentials = $credentialResolver->resolve();

        $this->sandbox = $credentials->isSandbox();
        $this->baseUrl = $this->sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function getSdkClient(): \PaypalServerSdkLib\PaypalServerSdkClient
    {
        $credentials = $this->credentialResolver->resolve();

        return PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $credentials->getClientId(),
                    $credentials->getClientSecret(),
                )
            )
            ->environment($this->sandbox ? Environment::SANDBOX : Environment::PRODUCTION)
            ->build();
    }

    public function getOrdersController(): \PaypalServerSdkLib\Controllers\OrdersController
    {
        return $this->getSdkClient()->getOrdersController();
    }

    public function getPaymentsController(): \PaypalServerSdkLib\Controllers\PaymentsController
    {
        return $this->getSdkClient()->getPaymentsController();
    }

    public function getSubscriptionsController(): \PaypalServerSdkLib\Controllers\SubscriptionsController
    {
        return $this->getSdkClient()->getSubscriptionsController();
    }

    public function http(): PendingRequest
    {
        $credentials = $this->credentialResolver->resolve();

        $tokenResponse = Http::withBasicAuth(
            $credentials->getClientId(),
            $credentials->getClientSecret()
        )->asForm()->post("{$this->baseUrl}/v1/oauth2/token", [
            'grant_type' => 'client_credentials',
        ]);

        $tokenResponse->throw();
        $accessToken = $tokenResponse->json('access_token');

        return Http::withToken($accessToken)
            ->withHeader('Content-Type', 'application/json')
            ->baseUrl($this->baseUrl);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $response = $this->http()->post('/v1/notifications/verify-webhook-signature', $payload);

        $response->throw();

        return ($response->json('verification_status') ?? '') === 'SUCCESS';
    }

    public function createPayout(array $data): array
    {
        $response = $this->http()->post('/v1/payments/payouts', $data);

        if ($response->failed()) {
            $body = $response->json();
            $details = '';

            if (isset($body['details']) && is_array($body['details'])) {
                foreach ($body['details'] as $detail) {
                    $details .= ($detail['field'] ?? '') . ': ' . ($detail['issue'] ?? '') . '; ';
                }
            }

            $message = $body['message'] ?? 'Unknown error';
            if ($details) {
                $message .= ' (' . $details . ')';
            }

            throw new \RuntimeException("PayPal Payouts API error: {$message}");
        }

        return $response->json();
    }

    public function getPayoutBatch(string $batchId): array
    {
        $response = $this->http()->get("/v1/payments/payouts/{$batchId}");

        $response->throw();

        return $response->json();
    }

    public function listWebhookEvents(array $params = []): array
    {
        $response = $this->http()->get('/v1/notifications/webhooks-events', $params);

        $response->throw();

        return $response->json();
    }

    public function getWebhookEvent(string $eventId): array
    {
        $response = $this->http()->get("/v1/notifications/webhooks-events/{$eventId}");

        $response->throw();

        return $response->json();
    }

    public function resendWebhookEvent(string $eventId, array $webhookIds): array
    {
        $response = $this->http()->post("/v1/notifications/webhooks-events/{$eventId}/resend", [
            'webhook_ids' => $webhookIds,
        ]);

        $response->throw();

        return $response->json();
    }

    public function listWebhooks(): array
    {
        $response = $this->http()->get('/v1/notifications/webhooks');

        $response->throw();

        return $response->json();
    }
}
