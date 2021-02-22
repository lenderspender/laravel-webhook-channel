<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Tests\Stubs;

use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use LenderSpender\LaravelWebhookChannel\Receiver\HasWebhookNotificationMessages;
use LenderSpender\LaravelWebhookChannel\Receiver\ReceivesWebhooks;
use LenderSpender\LaravelWebhookChannel\Receiver\WebhookData;

class NotifiableUser extends User implements ReceivesWebhooks
{
    use Notifiable;
    use HasWebhookNotificationMessages;

    public static string $webhookUrl = 'https://example.com';
    public static string $webhookSecret = 'foo';

    protected $table = 'users';
    protected $guarded = [];

    public function routeNotificationForWebhook(): ?WebhookData
    {
        return new WebhookData($this::$webhookUrl, $this::$webhookSecret);
    }
}
