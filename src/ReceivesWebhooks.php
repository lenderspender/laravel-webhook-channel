<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel;

interface ReceivesWebhooks
{
    public function routeNotificationForWebhook(): ?WebhookData;
}
