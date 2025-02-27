<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Receiver;

use Illuminate\Database\Eloquent\Model;

interface ReceivesWebhooks
{
    public function routeNotificationForWebhook(): ?WebhookData;

    /**
     * @return string
     */
    public function getMorphClass();

    /**
     * @return string
     */
    public function getKey();
}
