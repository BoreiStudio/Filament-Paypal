<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Jobs;

use BoreiStudio\FilamentPayPal\Features\Orders\Actions\SyncOrderFromApiAction;
use BoreiStudio\FilamentPayPal\Features\Webhooks\Models\WebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly WebhookEvent $event,
    ) {}

    public function handle(SyncOrderFromApiAction $syncOrder): void
    {
        try {
            $resourceId = $this->event->resource_id;

            if ($resourceId) {
                $syncOrder->execute($resourceId, $this->event->account_id);
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
