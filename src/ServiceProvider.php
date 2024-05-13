<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions;

use DragonCode\LaravelActions\Concerns\About;
use DragonCode\LaravelActions\Contracts\Notification;
use DragonCode\LaravelActions\Notifications\Beautiful;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    use About;

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();

            $this->registerAbout();
            $this->registerCommands();
            $this->registerMigrations();
            $this->registerNotifications();
        }
    }

    public function register(): void
    {
        $this->registerConfig();
    }

    protected function registerCommands(): void
    {
        $this->commands([
            Console\Actions::class,
            Console\Fresh::class,
            Console\Install::class,
            Console\Make::class,
            Console\Refresh::class,
            Console\Reset::class,
            Console\Rollback::class,
            Console\Status::class,
            Console\StubPublish::class,
            Console\Upgrade::class,
        ]);
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function registerNotifications(): void
    {
        $this->app->bind(Notification::class, Beautiful::class);
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/actions.php' => $this->app->configPath('actions.php'),
        ], 'config');
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/actions.php', 'actions');
    }
}
