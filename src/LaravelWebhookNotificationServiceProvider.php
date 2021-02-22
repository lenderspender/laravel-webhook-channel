<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel;

use Illuminate\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use LenderSpender\LaravelWebhookChannel\Listeners\WebhookCallFailedListener;
use LenderSpender\LaravelWebhookChannel\Listeners\WebhookCallSucceededListener;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class LaravelWebhookNotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)
            ->extend('webhook', fn (Application $app) => $app->make(WebhookChannel::class));

        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }

    public function register(): void
    {
        Event::listen(WebhookCallFailedEvent::class, WebhookCallFailedListener::class);
        Event::listen(WebhookCallSucceededEvent::class, WebhookCallSucceededListener::class);
    }
}
