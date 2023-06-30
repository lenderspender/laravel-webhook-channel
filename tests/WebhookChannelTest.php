<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Tests;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use LenderSpender\LaravelWebhookChannel\Enums\WebhookEvent;
use LenderSpender\LaravelWebhookChannel\Models\WebhookNotificationMessage;
use LenderSpender\LaravelWebhookChannel\Receiver\ReceivesWebhooks;
use LenderSpender\LaravelWebhookChannel\Receiver\WebhookData;
use LenderSpender\LaravelWebhookChannel\Receiver\WebhookMessage;
use LenderSpender\LaravelWebhookChannel\Tests\Stubs\NotifiableUser;
use LenderSpender\LaravelWebhookChannel\WebhookNotification;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookChannelTest extends TestCase
{
    private NotifiableUser $notifiable;

    public function setUp(): void
    {
        parent::setUp();

        /* @phpstan-ignore-next-line */
        $this->notifiable = NotifiableUser::query()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'foobar',
        ]);
    }

    public function test_a_webhook_notification_can_be_send(): void
    {
        $this->notifiable::$webhookUrl = 'https://postman-echo.com/post';
        $this->notifiable->notifyNow($this->getWebhookNotification());

        /** @var WebhookNotificationMessage $message */
        // @phpstan-ignore-next-line
        $message = $this->notifiable->webhookNotificationMessages()->first();

        self::assertNotNull($message->id);
        self::assertTrue($message->notifiable->is($this->notifiable)); /* @phpstan-ignore-line */
        self::assertEquals(WebhookEvent::DELIVERED, $message->event);
        self::assertEquals(['type' => 'foo_was_updated', 'data' => ['foo' => 'bar']], $message->webhook_message);
        self::assertStringContainsString('"type": "foo_was_updated",', $message->response);
        self::assertStringContainsString('"data": {
      "foo": "bar"
    }', $message->response);
        self::assertSame(Response::HTTP_OK, $message->response_status);
    }

    public function test_a_webhook_can_fail(): void
    {
        $this->notifiable::$webhookUrl = 'https://postman-echo.com/status/404';
        $this->notifiable->notifyNow($this->getWebhookNotification());

        /** @var WebhookNotificationMessage $message */
        // @phpstan-ignore-next-line
        $message = $this->notifiable->webhookNotificationMessages()->first();

        self::assertNotNull($message->id);
        self::assertTrue($message->notifiable->is($this->notifiable)); /* @phpstan-ignore-line */
        self::assertEquals(WebhookEvent::FAILED, $message->event);
        self::assertEquals(['type' => 'foo_was_updated', 'data' => ['foo' => 'bar']], $message->webhook_message);
        self::assertEquals('', $message->response);
        self::assertSame(Response::HTTP_NOT_FOUND, $message->response_status);
    }

    public function test_a_webhook_notification_message_is_created(): void
    {
        $this->notifiable::$webhookUrl = 'https://example.com';

        Event::fake([WebhookCallFailedEvent::class]);
        Event::fake([WebhookCallSucceededEvent::class]);

        $this->notifiable->notifyNow($this->getWebhookNotification());

        /** @var WebhookNotificationMessage $message */
        $message = WebhookNotificationMessage::query()->firstOrFail();

        self::assertEquals(WebhookEvent::CREATED, $message->event);
    }

    public function test_a_webhook_notification_is_not_sent_when_there_is_no_webhook_url_set(): void
    {
        Event::fake();

        $notifiable = new class() extends AnonymousNotifiable implements ReceivesWebhooks {
            public function routeNotificationForWebhook(): ?WebhookData
            {
                return null;
            }

            public function getMorphClass(): string
            {
                return 'anonymise-notification';
            }

            public function getKey(): string
            {
                return 'foo-bar';
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
