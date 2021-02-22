<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel;

use LenderSpender\LaravelWebhookChannel\Enums\WebhookEvent;
use LenderSpender\LaravelWebhookChannel\Models\WebhookNotificationMessage;
use LenderSpender\LaravelWebhookChannel\Receiver\ReceivesWebhooks;

class WebhookChannel
{
    public function send(ReceivesWebhooks $notifiable, WebhookNotification $notification): void
    {
        $webhookData = $notifiable->routeNotificationForWebhook();

        if (! $webhookData) {
            return;
        }

        $webhookMessage = $notification->toWebhook($notifiable);

        $message = WebhookNotificationMessage::query()->create([
            'id' => $notification->id,
            'event' => WebhookEvent::CREATED(),
            'notifiable_id' => $notifiable->getKey(),
            'notifiable_type' => $notifiable->getMorphClass(),
            'webhook_message' => $webhookMessage->toArray(),
        ]);

        $message->callWebhook();
    }
}
