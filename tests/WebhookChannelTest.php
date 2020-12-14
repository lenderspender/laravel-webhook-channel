<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Tests;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use LenderSpender\LaravelWebhookChannel\ReceivesWebhooks;
use LenderSpender\LaravelWebhookChannel\WebhookChannel;
use LenderSpender\LaravelWebhookChannel\WebhookData;
use LenderSpender\LaravelWebhookChannel\WebhookMessage;
use LenderSpender\LaravelWebhookChannel\WebhookNotification;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookChannelTest extends TestCase
{
    private WebhookChannel $webhookChannel;

    public function setUp(): void
    {
        parent::setUp();

        $this->webhookChannel = $this->app->make(WebhookChannel::class);
    }

    public function test_a_webhook_notification_can_be_send(): void
    {
        Event::fake([WebhookCallSucceededEvent::class]);

        $notifiable = new class() extends AnonymousNotifiable implements ReceivesWebhooks {
            public function routeNotificationForWebhook(): ?WebhookData
            {
                return new WebhookData('https://example.com', 'foo');
            }
        };

        $notifiable->notifyNow($this->getWebhookNotification());

        Event::assertDispatched(WebhookCallSucceededEvent::class, function (WebhookCallSucceededEvent $event) {
            self::assertSame('foo_was_updated', $event->payload['type']);
            self::assertSame('bar', $event->payload['data']['foo']);
            self::assertNotNull($event->response->getBody()->getContents());
            self::assertSame('https://example.com', $event->webhookUrl);
            self::assertSame('047de5a643fa9faa4ce2de603ae4311d5410802afd4f480520d0dcb36c5abff3', $event->headers['Signature']);

            return true;
        });
    }

    public function test_a_webhook_notification_is_not_sent_when_there_is_no_webhook_url_set(): void
    {
        Event::fake();

        $notifiable = new class() extends AnonymousNotifiable implements ReceivesWebhooks {
            public function routeNotificationForWebhook(): ?WebhookData
            {
                return null;
            }
        };

        $notifiable->notifyNow($this->getWebhookNotification());

        Event::assertNotDispatched(WebhookCallSucceededEvent::class);
    }

    private function getWebhookNotification(): Notification
    {
        return new class() extends Notification implements WebhookNotification {
            public function via(): array
            {
                return ['webhook'];
            }

            public function toWebhook(ReceivesWebhooks $notiable): WebhookMessage
            {
                $resource = new class([]) extends JsonResource {
                    public function toArray($request): array
                    {
                        return [
                            'foo' => 'bar',
                        ];
                    }
                };

                return new WebhookMessage('foo_was_updated', $resource);
            }
        };
    }
}
