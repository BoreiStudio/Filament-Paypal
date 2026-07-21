<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Jobs;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Models\WebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSubscriptionWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly WebhookEvent $event,
    ) {}

    public function handle(): void
    {
        try {
            $payload = $this->event->raw_payload;
            $resource = $payload['resource'] ?? [];
            $subscriptionId = $resource['id'] ?? $this->event->resource_id;

            if ($resource && $subscriptionId) {
                $statusMap = [
                    'APPROVAL_PENDING' => 'APPROVAL_PENDING',
                    'APPROVED' => 'APPROVED',
                    'ACTIVE' => 'ACTIVE',
                    'SUSPENDED' => 'SUSPENDED',
                    'CANCELLED' => 'CANCELLED',
                    'EXPIRED' => 'EXPIRED',
                ];

                $paypalStatus = $resource['status'] ?? '';
                $localStatus = $statusMap[$paypalStatus] ?? null;

                if ($localStatus) {
                    Subscription::updateOrCreate(
                        [
                            'account_id' => $this->event->account_id,
                            'paypal_subscription_id' => $subscriptionId,
                        ],
                        [
                            'status' => $localStatus,
                            'subscriber_email' => $resource['subscriber']['email_address'] ?? null,
                            'subscriber_name' => isset($resource['subscriber']['name'])
                                ? ($resource['subscriber']['name']['given_name'] ?? '') . ' ' . ($resource['subscriber']['name']['surname'] ?? '')
                                : null,
                            'start_time' => $resource['start_time'] ?? null,
                            'next_billing_time' => $resource['billing_info']['next_billing_time'] ?? null,
                            'last_payment_time' => $resource['billing_info']['last_payment']['time'] ?? null,
                            'last_payment_amount' => $resource['billing_info']['last_payment']['amount']['value'] ?? null,
                            'failed_payments_count' => $resource['billing_info']['failed_payments_count'] ?? 0,
                            'paypal_response' => $resource,
                        ]
                    );
                }
            }

            $this->event->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->event->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
