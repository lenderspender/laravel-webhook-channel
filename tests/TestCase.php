<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Tests;

use LenderSpender\LaravelWebhookChannel\LaravelWebhookNotificationServiceProvider;
use Spatie\WebhookServer\WebhookServerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            WebhookServerServiceProvider::class,
            LaravelWebhookNotificationServiceProvider::class,
        ];
    }
}
