<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookNotificationMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_notification_messages', function (Blueprint $table) {
            $table->uuid('id');
            $table->morphs('notifiable');
            $table->string('event');
            $table->text('webhook_message');
            $table->text('response')->nullable();
            $table->integer('response_status')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('webhook_notification_messages');
    }
}
