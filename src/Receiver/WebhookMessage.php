<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Receiver;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookMessage implements Arrayable
{
    public string $type;
    public JsonResource $resource;

    public function __construct(string $type, JsonResource $resource)
    {
        $this->type = $type;
        $this->resource = $resource;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => $this->resource->toArray(new Request()),
        ];
    }
}
