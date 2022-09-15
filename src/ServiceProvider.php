<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions;

use DragonCode\LaravelActions\Concerns\About;
use DragonCode\LaravelActions\Concerns\Anonymous;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    use About;
    use Anonymous;

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerAbout();
            $this->registerMigrations();
        }
    }

    protected function registerCommands(): void
    {
        $this->commands([
            Console\Fresh::class,
            Console\Install::class,
            Console\Make::class,
            Console\Migrate::class,
            Console\Refresh::class,
            Console\Reset::class,
            Console\Rollback::class,
            Console\Status::class,
            Console\Upgrade::class,
        ]);
    }

    protected function registerMigrations(): void
    {
        $this->allowAnonymousMigrations()
            ? $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/anonymous')
            : $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/named');
    }
}
