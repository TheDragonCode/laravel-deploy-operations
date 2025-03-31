<?php

declare(strict_types=1);

namespace Tests\Concerns;

trait Database
{
    protected string $database = 'testing';

    protected string $table = 'foo_operations';

    protected function setDatabase($app): void
    {
        $app['config']->set('database.default', $this->database);

        $app['config']->set('database.connections.' . $this->database, [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('deploy-operations.connection', $this->database);
        $app['config']->set('deploy-operations.table', $this->table);
    }

    protected function freshDatabase(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../fixtures/migrations');

        $this->artisan('migrate')->run();
    }
}
