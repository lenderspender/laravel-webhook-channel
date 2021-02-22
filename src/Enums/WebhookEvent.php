<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Enums;

use LenderSpender\LaravelEnums\Enum;

/**
 * @method static self CREATED()
 * @method static self DELIVERED()
 * @method static self FAILED()
 */
class WebhookEvent extends Enum
{
    private const CREATED = 'created';
    private const DELIVERED = 'delivered';
    private const FAILED = 'failed';
}
