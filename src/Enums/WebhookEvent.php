<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Enums;

enum WebhookEvent: string
{
    case CREATED = 'created';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
}
