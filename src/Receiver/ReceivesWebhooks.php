<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Receiver;

interface ReceivesWebhooks
{
    public function routeNotificationForWebhook(): ?WebhookData;
}
