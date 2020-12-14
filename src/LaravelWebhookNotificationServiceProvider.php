<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel;

use Illuminate\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

class LaravelWebhookNotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)
            ->extend('webhook', fn (Application $app) => $app->make(WebhookChannel::class));
    }
}
