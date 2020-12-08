<?php

namespace Helldar\LaravelActions;

use Helldar\LaravelActions\Console\Install;
use Helldar\LaravelActions\Console\Make;
use Helldar\LaravelActions\Console\Migrate;
use Helldar\LaravelActions\Console\Refresh;
use Helldar\LaravelActions\Console\Reset;
use Helldar\LaravelActions\Console\Rollback;
use Helldar\LaravelActions\Console\Status;
use Helldar\LaravelActions\Constants\Action;
use Helldar\LaravelActions\Constants\Command;
use Helldar\LaravelActions\Support\MigrationCreator;
use Helldar\LaravelActions\Support\Migrator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class ServiceProvider extends BaseServiceProvider
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

    public function register()
    {
        $this->registerConfig();
        $this->registerRepository();
        $this->registerMigrator();
        $this->registerCreator();
        $this->registerCommands($this->commands);
    }

    public function boot()
    {
        $this->bootConfig();
    }

    public function provides()
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
            return new DatabaseMigrationRepository($app['db'], $app['config']['actions.table']);
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
            return new MigrationCreator($app['files'], __DIR__ . '/../resources/stub');
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
        $this->app->singleton(Command::REFRESH, function ($app) {
            return new Refresh();
        });
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/actions.php',
            'actions'
        );
    }

    protected function bootConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/actions.php' => $this->app->configPath('actions.php'),
        ], 'config');
    }
}
