# Laravel webhook channel
Laravel webhook chanel allows you to send `JsonResource` objects as notifications to your users.

## Installation

You can install the package via composer:
```bash
composer require lenderspender/laravel-webhook-channel
```

## Installation
Implement `ReceivesWebhooks` on the model that you would like to receive webhooks.

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use LenderSpender\LaravelWebhookChannel\ReceivesWebhooks;
use LenderSpender\LaravelWebhookChannel\WebhookData;

class User extends Model implements ReceivesWebhooks
{
    use Notifiable;
    
    public function routeNotificationForWebhook() : ?WebhookData
    {
        return new WebhookData('https://example.com/webhooks/', 'users-webhook-secret');
    }
}
``` 

## Creating webhook notifications
To allow your notifications to send webhook data you simply need to implement `WebhookNotification`. It accepts [Eloquent: API Resources](https://laravel.com/docs/master/eloquent-resources).

```php
<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use LenderSpender\LaravelWebhookChannel\ReceivesWebhooks;use LenderSpender\LaravelWebhookChannel\WebhookMessage;use LenderSpender\LaravelWebhookChannel\WebhookNotification;

class StatusUpdatedNotification extends Notification implements WebhookNotification
{
    use Notifiable;
    
    public function routeNotificationForWebhook(ReceivesWebhooks $notifiable) : WebhookMessage
    {
        $resource = new UserResource($notifiable);
        
        return new WebhookMessage('foo_was_updated', $resource);
    }
}
```

## Notifying


```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class NotificationController
{
    public function __invoke()
    {
        auth()->user()->notify(new StatusUpdatedNotification());
    }
}
```
