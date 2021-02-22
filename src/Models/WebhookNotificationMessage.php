<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\DatabaseNotification;
use LenderSpender\LaravelEnums\Models\Traits\CastsEnums;
use LenderSpender\LaravelWebhookChannel\Enums\WebhookEvent;
use Spatie\WebhookServer\WebhookCall;

/**
 * @property string                                                         $id
 * @property string|int                                                     $notifiable_id
 * @property string                                                         $notifiable_type
 * @property \LenderSpender\LaravelWebhookChannel\Receiver\ReceivesWebhooks $notifiable
 * @property DatabaseNotification|null                                       $databaseNotification
 * @property array                                                          $webhook_message
 * @property string|null                                                    $response
 * @property int|null                                                       $response_status
 * @property \Carbon\CarbonInterface|null                                   $handled_at
 * @property \LenderSpender\LaravelWebhookChannel\Enums\WebhookEvent        $event
 * @property \Carbon\CarbonInterface                                        $created_at
 * @property \Carbon\CarbonInterface                                        $updated_at
 */
class WebhookNotificationMessage extends Model
{
    use CastsEnums;

    public $incrementing = false;

    /** @var array */
    protected $guarded = [];

    /** @var string[] */
    protected $casts = [
        'webhook_message' => 'array',
        'response_status' => 'int',
        'handled_at' => 'datetime'
    ];

    /** @var string[] */
    protected $enums = [
        'event' => WebhookEvent::class,
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function databaseNotification(): BelongsTo
    {
        return $this->belongsTo(DatabaseNotification::class, 'id', 'id');
    }

    public function callWebhook(): void
    {
        $webhookData = $this->notifiable->routeNotificationForWebhook();

        WebhookCall::create()
            ->url($webhookData->url)
            ->useSecret($webhookData->secret ?? '')
            ->withTags(['webhook'])
            ->uuid($this->id)
            ->payload($this->webhook_message)
            ->dispatchNow();
    }
}
