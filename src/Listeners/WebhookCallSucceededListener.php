<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Listeners;

use LenderSpender\LaravelWebhookChannel\Enums\WebhookEvent;
use LenderSpender\LaravelWebhookChannel\Models\WebhookNotificationMessage;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookCallSucceededListener
{
    public function handle(WebhookCallSucceededEvent $webhookCallSucceededEvent): void
    {
        /** @var WebhookNotificationMessage|null $notification */
        $notification = WebhookNotificationMessage::query()->find($webhookCallSucceededEvent->uuid);

        if (! $notification) {
            return;
        }

        $response = optional($webhookCallSucceededEvent->response);

        $notification->update([
            'response' => (string) $response->getBody(),
            'response_status' => (string) $response->getStatusCode(),
            'handled_at' => now(),
            'event' => WebhookEvent::DELIVERED,
        ]);
    }
}
