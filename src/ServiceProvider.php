<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations;

use DragonCode\LaravelDeployOperations\Concerns\HasAbout;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    use HasAbout;

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishStub();

            $this->registerAbout();
            $this->registerCommands();
            $this->registerMigrations();
        }
    }

    public function register(): void
    {
        $this->registerConfig();
    }

    protected function registerCommands(): void
    {
        $this->commands([
            Console\OperationsCommand::class,
            Console\FreshCommand::class,
            Console\InstallCommand::class,
            Console\MakeCommand::class,
            Console\RefreshCommand::class,
            Console\ResetCommand::class,
            Console\RollbackCommand::class,
            Console\StatusCommand::class,
        ]);
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/deploy-operations.php' => $this->app->configPath('deploy-operations.php'),
        ], 'config');
    }

    protected function publishStub(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/stubs/deploy-operation.stub' => $this->app->basePath(
                'stubs/deploy-operation.stub'
            ),
        ], 'stubs');
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/deploy-operations.php', 'deploy-operations');
    }
}
