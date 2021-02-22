<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel;

use LenderSpender\LaravelWebhookChannel\Receiver\ReceivesWebhooks;
use LenderSpender\LaravelWebhookChannel\Receiver\WebhookMessage;

/**
 * @mixin \Illuminate\Notifications\Notification
 */
interface WebhookNotification
{
    public function toWebhook(ReceivesWebhooks $notifiable): WebhookMessage;
}
