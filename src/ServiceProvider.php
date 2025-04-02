<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations;

use DragonCode\LaravelDeployOperations\Concerns\HasAbout;
use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;
use DragonCode\LaravelDeployOperations\Listeners\MigrationEndedListener;
use Illuminate\Database\Events\MigrationEnded;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use function config;

class ServiceProvider extends BaseServiceProvider
{
    use HasAbout;

    public function boot(): void
    {
        $this->registerEvents();
        $this->bootConfig();

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
            Console\RollbackCommand::class,
            Console\StatusCommand::class,
        ]);
    }

    protected function registerEvents(): void
    {
        Event::listen(MigrationEnded::class, MigrationEndedListener::class);
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/deploy-operations.php' => $this->app->configPath('deploy-operations.php'),
        ], ['config', 'deploy-operations']);
    }

    protected function publishStub(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/stubs/deploy-operation.stub' => $this->app->basePath(
                'stubs/deploy-operation.stub'
            ),
        ], ['stubs', 'deploy-operations']);
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/deploy-operations.php', 'deploy-operations');
    }

    protected function bootConfig(): void
    {
        $this->app->bind(ConfigData::class, static fn () => ConfigData::from(
            config('deploy-operations')
        ));
    }
}
