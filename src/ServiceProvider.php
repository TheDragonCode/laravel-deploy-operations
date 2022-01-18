<?php

namespace DragonCode\LaravelActions;

use DragonCode\LaravelActions\Console\Install;
use DragonCode\LaravelActions\Console\Make;
use DragonCode\LaravelActions\Console\Migrate;
use DragonCode\LaravelActions\Console\Refresh;
use DragonCode\LaravelActions\Console\Reset;
use DragonCode\LaravelActions\Console\Rollback;
use DragonCode\LaravelActions\Console\Status;
use DragonCode\LaravelActions\Constants\Action;
use DragonCode\LaravelActions\Constants\Command;
use DragonCode\LaravelActions\Support\MigrationCreator;
use DragonCode\LaravelActions\Support\Migrator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    protected $commands = [
        'Migrate'         => Command::MIGRATE,
        'MigrateInstall'  => Command::INSTALL,
        'MigrateMake'     => Command::MAKE,
        'MigrateRefresh'  => Command::REFRESH,
        'MigrateReset'    => Command::RESET,
        'MigrateRollback' => Command::ROLLBACK,
        'MigrateStatus'   => Command::STATUS,
    ];

    public function register(): void
    {
        $this->registerConfig();
        $this->registerRepository();
        $this->registerMigrator();
        $this->registerCreator();
        $this->registerCommands($this->commands);
    }

    public function provides(): array
    {
        return array_merge([
            Action::MIGRATOR,
            Action::REPOSITORY,
            Action::CREATOR,
        ], array_values($this->commands));
    }

    protected function registerRepository(): void
    {
        $this->app->singleton(Action::REPOSITORY, static function ($app) {
            return new DatabaseMigrationRepository($app['db'], $app['config']['database.actions']);
        });
    }

    protected function registerMigrator(): void
    {
        $this->app->singleton(Action::MIGRATOR, static function ($app) {
            return new Migrator($app[Action::REPOSITORY], $app['db'], $app['files'], $app['events']);
        });
    }

    protected function registerCreator(): void
    {
        $this->app->singleton(Action::CREATOR, static function ($app) {
            return new MigrationCreator($app['files'], __DIR__ . '/../resources/stubs');
        });
    }

    protected function registerCommands(array $commands): void
    {
        foreach (array_keys($commands) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands(array_values($commands));
    }

    protected function registerMigrateCommand(): void
    {
        $this->app->singleton(Command::MIGRATE, static function ($app) {
            return new Migrate($app[Action::MIGRATOR], $app[Dispatcher::class]);
        });
    }

    protected function registerMigrateStatusCommand(): void
    {
        $this->app->singleton(Command::STATUS, static function ($app) {
            return new Status($app[Action::MIGRATOR]);
        });
    }

    protected function registerMigrateInstallCommand(): void
    {
        $this->app->singleton(Command::INSTALL, static function ($app) {
            return new Install($app[Action::REPOSITORY]);
        });
    }

    protected function registerMigrateMakeCommand(): void
    {
        $this->app->singleton(Command::MAKE, function ($app) {
            return new Make(
                $app[Action::CREATOR],
                $app['composer']
            );
        });
    }

    protected function registerMigrateRollbackCommand(): void
    {
        $this->app->singleton(Command::ROLLBACK, function ($app) {
            return new Rollback($app[Action::MIGRATOR]);
        });
    }

    protected function registerMigrateResetCommand(): void
    {
        $this->app->singleton(Command::RESET, function ($app) {
            return new Reset($app[Action::MIGRATOR]);
        });
    }

    protected function registerMigrateRefreshCommand(): void
    {
        $this->app->singleton(Command::REFRESH, function () {
            return new Refresh();
        });
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/database.php',
            'database'
        );
    }
}
