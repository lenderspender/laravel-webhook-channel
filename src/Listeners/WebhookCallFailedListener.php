<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Listeners;

use GuzzleHttp\Psr7\MessageTrait;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Optional;
use LenderSpender\LaravelWebhookChannel\Enums\WebhookEvent;
use LenderSpender\LaravelWebhookChannel\Models\WebhookNotificationMessage;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;

class WebhookCallFailedListener
{
    public function handle(WebhookCallFailedEvent $webhookCallFailedEvent): void
    {
        /** @var WebhookNotificationMessage|null $notification */
        $notification = WebhookNotificationMessage::query()->find($webhookCallFailedEvent->uuid);

        if (! $notification) {
            return;
        }

        /** @var Response|null $response */
        $response = optional($webhookCallFailedEvent->response);

        $notification->update([
            'response' => (string) $response->getBody(),
            'response_status' => (string) $response->getStatusCode(),
            'handled_at' => now(),
            'event' => WebhookEvent::FAILED,
        ]);
    }
}
