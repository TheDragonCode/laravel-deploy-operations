<?php

namespace Tests\Concerns;

trait Database
{
    protected string $database = 'testing';

    protected string $table = 'foo_actions';

    protected function setDatabase($app): void
    {
        $app['config']->set('database.default', $this->database);

        $app['config']->set('database.connections.' . $this->database, [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('actions.connection', $this->database);
        $app['config']->set('actions.table', $this->table);
    }

    protected function freshDatabase(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../fixtures/migrations');

        $this->artisan('migrate')->run();
    }
}
