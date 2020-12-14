<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel;

/**
 * @mixin \Illuminate\Notifications\Notification
 */
interface WebhookNotification
{
    public function toWebhook(ReceivesWebhooks $notifiable): WebhookMessage;
}
