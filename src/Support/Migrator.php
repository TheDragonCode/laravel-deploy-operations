<?php

namespace DragonCode\LaravelActions\Support;

use DragonCode\Contracts\LaravelActions\Actionable as ActionableContract;
use DragonCode\LaravelActions\Concerns\Anonymous;
use DragonCode\LaravelActions\Concerns\Infoable;
use Illuminate\Database\Migrations\Migrator as BaseMigrator;
use Illuminate\Support\Facades\DB;
use Throwable;

class Migrator extends BaseMigrator
{
    use Infoable;
    use Anonymous;

    protected $is_before = false;

    public function usingConnection($name, callable $callback)
    {
        $prev = $this->resolver->getDefaultConnection();

        $this->setConnection($name);

        return tap($callback(), function () use ($prev) {
            $this->setConnection($prev);
        });
    }

    public function runPending(array $migrations, array $options = [])
    {
        $this->is_before = $options['before'] ?? false;

        return parent::runPending($migrations, $options);
    }

    /**
     * Run "up" a migration instance.
     *
     * @param string $file
     * @param int $batch
     * @param bool $pretend
     *
     * @throws Throwable
     */
    protected function runUp($file, $batch, $pretend)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        if ($this->allowAnonymous()) {
            $migration = $this->resolvePath($file);

            $name = $this->getMigrationName($file);
        }
        else {
            $migration = $this->resolve(
                $name  = $this->getMigrationName($file)
            );
        }

        if (! $this->allowEnvironment($migration)) {
            $this->note("<info>Migrate:</info>  {$name} was skipped on this environment");

            return;
        }

        if ($this->disallowBefore($migration)) {
            $this->note("<info>Migrate:</info>  {$name} was omitted because the 'before' parameter is enabled.");

            return;
        }

        if ($pretend) {
            $this->pretendToRun($migration, 'up');

            return;
        }

        $this->note("<comment>Migrating:</comment> {$name}");

        $startTime = microtime(true);

        $this->runMigration($migration, 'up');

        $runTime = number_format((microtime(true) - $startTime) * 1000, 2);

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        if ($this->allowLogging($migration)) {
            $this->repository->log($name, $batch);
        }

        $this->note("<info>Migrated:</info>  {$name} ({$runTime}ms)");
    }

    /**
     * Run "down" a migration instance.
     *
     * @param string $file
     * @param object $migration
     * @param bool $pretend
     *
     * @throws Throwable
     */
    protected function runDown($file, $migration, $pretend)
    {
        if ($this->allowAnonymous()) {
            $instance = $this->resolvePath($file);

            $name = $this->getMigrationName($file);
        }
        else {
            $instance = $this->resolve(
                $name = $this->getMigrationName($file)
            );
        }

        if (! $this->allowEnvironment($instance)) {
            $this->note("<info>Rolling back:</info>  {$name} was skipped on this environment");

            return;
        }

        parent::runDown($file, $migration, $pretend);
    }

    /**
     * Starts the execution of code, starting database transactions, if necessary.
     *
     * @param object $migration
     * @param string $method
     *
     * @throws Throwable
     */
    protected function runMigration($migration, $method)
    {
        $this->runMigrationHandle($migration, function ($migration) use ($method) {
            if ($this->enabledTransactions($migration)) {
                DB::transaction(function () use ($migration, $method) {
                    parent::runMigration($migration, $method);
                }, $this->transactionAttempts($migration));

                return;
            }

            parent::runMigration($migration, $method);
        });
    }

    /**
     * Whether it is necessary to record information about the execution in the database.
     *
     * @param \DragonCode\Contracts\LaravelActions\Actionable|object $migration
     *
     * @return bool
     */
    protected function allowLogging(ActionableContract $migration): bool
    {
        return $migration->isOnce();
    }

    /**
     * Whether the action needs to be executed in the current environment.
     *
     * @param \DragonCode\Contracts\LaravelActions\Actionable|object $migration
     *
     * @return bool
     */
    protected function allowEnvironment(ActionableContract $migration): bool
    {
        $environment = config('app.env', 'production');

        $on     = $migration->onEnvironment();
        $except = $migration->exceptEnvironment();
        $allow  = $migration->allow();

        if (! $allow) {
            return false;
        }

        if (! empty($on) && ! in_array($environment, $on)) {
            return false;
        }

        return ! (! empty($except) && in_array($environment, $except));
    }

    /**
     * Whether it is necessary to call database transactions at runtime.
     *
     * @param \DragonCode\LaravelActions\Support\Actionable|object $migration
     *
     * @return bool
     */
    protected function enabledTransactions(ActionableContract $migration): bool
    {
        return $migration->enabledTransactions();
    }

    /**
     * The number of attempts to execute a request within a transaction before throwing an error.
     *
     * @param \DragonCode\LaravelActions\Support\Actionable|object $migration
     *
     * @return int
     */
    protected function transactionAttempts(ActionableContract $migration): int
    {
        $value = $migration->transactionAttempts();

        return (int) abs($value);
    }

    /**
     * Defines a possible "pre-launch" of the action.
     *
     * @param \DragonCode\Contracts\LaravelActions\Actionable|object $migration
     *
     * @return bool
     */
    protected function disallowBefore(ActionableContract $migration): bool
    {
        return $this->is_before && ! $migration->hasBefore();
    }

    /**
     * @param \DragonCode\Contracts\LaravelActions\Actionable|object $migration
     * @param callable $handle
     *
     * @throws Throwable
     *
     * @return void
     */
    protected function runMigrationHandle(ActionableContract $migration, callable $handle)
    {
        try {
            $handle($migration);

            $this->runSuccess($migration);
        }
        catch (Throwable $e) {
            $this->runFailed($migration);

            throw $e;
        }
    }

    /**
     * @param \DragonCode\Contracts\LaravelActions\Actionable|object $migration
     *
     * @return void
     */
    protected function runSuccess(ActionableContract $migration): void
    {
        $migration->success();
    }

    /**
     * @param \DragonCode\Contracts\LaravelActions\Actionable|object $migration
     *
     * @return void
     */
    protected function runFailed(ActionableContract $migration): void
    {
        $migration->failed();
    }
}
