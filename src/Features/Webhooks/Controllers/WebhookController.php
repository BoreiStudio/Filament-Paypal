<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Controllers;

use BoreiStudio\FilamentPayPal\Features\Webhooks\Jobs\ProcessOrderWebhookJob;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Jobs\ProcessPaymentWebhookJob;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Jobs\ProcessSubscriptionWebhookJob;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Models\WebhookEvent;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Support\WebhookSignatureVerifier;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Http\Request;

class WebhookController
{
    public function __invoke(Request $request, ?string $account = null)
    {
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? '';
        $eventId = $payload['id'] ?? null;

        $accountModel = null;
        if ($account) {
            $accountModel = PaypalAccount::find($account);
        }
        if (! $accountModel) {
            $accountModel = PaypalAccount::whereNull('tenant_id')
                ->whereNull('tenant_type')
                ->where('status', 'connected')
                ->first();
        }

        $signatureValid = null;
        if ($accountModel && config('paypal.webhooks.verify_signature', true)) {
            $signatureValid = app(WebhookSignatureVerifier::class)
                ->verify($request->headers->all(), $payload);
        }

        if ($eventId) {
            $event = WebhookEvent::firstOrCreate(
                ['paypal_event_id' => $eventId],
                [
                    'account_id' => $accountModel?->id,
                    'event_type' => $eventType,
                    'resource_type' => $payload['resource_type'] ?? null,
                    'resource_id' => $payload['resource']['id'] ?? null,
                    'summary' => $payload['summary'] ?? null,
                    'signature_valid' => $signatureValid,
                    'status' => 'pending',
                    'raw_payload' => $payload,
                ]
            );
        } else {
            $event = WebhookEvent::create([
                'account_id' => $accountModel?->id,
                'paypal_event_id' => null,
                'event_type' => $eventType,
                'resource_type' => $payload['resource_type'] ?? null,
                'resource_id' => $payload['resource']['id'] ?? null,
                'summary' => $payload['summary'] ?? null,
                'signature_valid' => $signatureValid,
                'status' => 'pending',
                'raw_payload' => $payload,
            ]);
        }

        $this->dispatchJob($event);

        return response()->json(['status' => 'ok']);
    }

    private function dispatchJob(WebhookEvent $event): void
    {
        $eventType = $event->event_type;

        if (str_starts_with($eventType, 'CHECKOUT.ORDER.')) {
            ProcessOrderWebhookJob::dispatch($event);
        } elseif (str_starts_with($eventType, 'PAYMENT.')) {
            ProcessPaymentWebhookJob::dispatch($event);
        } elseif (str_starts_with($eventType, 'BILLING.SUBSCRIPTION.')) {
            ProcessSubscriptionWebhookJob::dispatch($event);
        } else {
            $event->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);
        }
    }
}
