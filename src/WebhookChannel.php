<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel;

use Spatie\WebhookServer\WebhookCall;

class WebhookChannel
{
    public function send(ReceivesWebhooks $notifiable, WebhookNotification $notification): void
    {
        $webhookData = $notifiable->routeNotificationForWebhook();

        if (! $webhookData) {
            return;
        }

        $webhookMessage = $notification->toWebhook($notifiable);

        WebhookCall::create()
            ->url($webhookData->url)
            ->useSecret($webhookData->secret ?? '')
            ->withTags(['webhook'])
            ->uuid($notification->id)
            ->payload($webhookMessage->toArray())
            ->dispatchNow();
    }
}
