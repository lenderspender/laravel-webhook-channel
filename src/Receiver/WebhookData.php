<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Receiver;

class WebhookData
{
    public string $url;
    public ?string $secret = null;

    public function __construct(string $url, string $secret = null)
    {
        $this->url = $url;
        $this->secret = $secret;
    }
}
