<?php

namespace Helldar\LaravelActions\Support;

use Helldar\LaravelActions\Traits\Infoable;
use Illuminate\Database\Migrations\Migrator as BaseMigrator;
use Illuminate\Support\Facades\DB;

final class Migrator extends BaseMigrator
{
    use Infoable;

    public function usingConnection($name, callable $callback)
    {
        $prev = $this->resolver->getDefaultConnection();

        $this->setConnection($name);

        return tap($callback(), function () use ($prev) {
            $this->setConnection($prev);
        });
    }

    /**
     * Run "up" a migration instance.
     *
     * @param  string  $file
     * @param  int  $batch
     * @param  bool  $pretend
     */
    protected function runUp($file, $batch, $pretend)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $migration = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        if ($pretend) {
            return $this->pretendToRun($migration, 'up');
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
     * Starts the execution of code, starting database transactions, if necessary.
     *
     * @param  object  $migration
     * @param  string  $method
     */
    protected function runMigration($migration, $method)
    {
        if ($this->enabledTransactions($migration)) {
            DB::transaction(function () use ($migration, $method) {
                parent::runMigration($migration, $method);
            }, $this->transactionAttempts($migration));

            return;
        }

        parent::runMigration($migration, $method);
    }

    /**
     * Whether it is necessary to record information about the execution in the database.
     *
     * @param  object  $migration
     *
     * @return bool
     */
    protected function allowLogging($migration): bool
    {
        return $migration->isOnce();
    }

    /**
     * Whether it is necessary to call database transactions at runtime.
     *
     * @param  \Helldar\LaravelActions\Support\Actionable|object  $migration
     *
     * @return bool
     */
    protected function enabledTransactions($migration): bool
    {
        return $migration->enabledTransactions();
    }

    /**
     * The number of attempts to execute a request within a transaction before throwing an error.
     *
     * @param  \Helldar\LaravelActions\Support\Actionable|object  $migration
     *
     * @return int
     */
    protected function transactionAttempts($migration): int
    {
        $value = $migration->transactionAttempts();

        return (int) abs($value);
    }
}
