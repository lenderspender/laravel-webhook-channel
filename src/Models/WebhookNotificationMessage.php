<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\DatabaseNotification;
use LenderSpender\LaravelWebhookChannel\Enums\WebhookEvent;
use LenderSpender\LaravelWebhookChannel\Receiver\ReceivesWebhooks;
use Spatie\WebhookServer\WebhookCall;

/**
 * @property string                                                         $id
 * @property string|int                                                     $notifiable_id
 * @property string                                                         $notifiable_type
 * @property \LenderSpender\LaravelWebhookChannel\Receiver\ReceivesWebhooks $notifiable
 * @property DatabaseNotification|null                                      $databaseNotification
 * @property array                                                          $webhook_message
 * @property string|null                                                    $response
 * @property int|null                                                       $response_status
 * @property \Carbon\CarbonInterface|null                                   $handled_at
 * @property WebhookEvent                                                   $event
 * @property \Carbon\CarbonInterface                                        $created_at
 * @property \Carbon\CarbonInterface                                        $updated_at
 */
class WebhookNotificationMessage extends Model
{
    public $incrementing = false;

    /** @var array<string> */
    protected $guarded = [];

    /** @var string[] */
    protected $casts = [
        'event' => WebhookEvent::class,
        'webhook_message' => 'array',
        'response_status' => 'int',
        'handled_at' => 'datetime',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<DatabaseNotification, $this>
     */
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
            ->dispatch();
    }
}
