<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Receiver;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use LenderSpender\LaravelWebhookChannel\Models\WebhookNotificationMessage;

/**
 * @property \Illuminate\Support\Collection|\LenderSpender\LaravelWebhookChannel\Models\WebhookNotificationMessage[] $webhookNotificationMessages
 */
trait HasWebhookNotificationMessages
{
    public function webhookNotificationMessages(): MorphMany
    {
        return $this->morphMany(WebhookNotificationMessage::class, 'notifiable');
    }
}
