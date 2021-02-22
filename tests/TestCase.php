<?php

declare(strict_types=1);

namespace LenderSpender\LaravelWebhookChannel\Tests;

use LenderSpender\LaravelWebhookChannel\LaravelWebhookNotificationServiceProvider;
use Spatie\WebhookServer\WebhookServerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench']);
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            WebhookServerServiceProvider::class,
            LaravelWebhookNotificationServiceProvider::class,
        ];
    }
}
