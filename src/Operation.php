<?php

namespace DragonCode\LaravelDeployOperations;

use DragonCode\LaravelDeployOperations\Concerns\Artisan;
use Illuminate\Support\Arr;

abstract class Operation
{
    use Artisan;

    /**
     * Determines the type of launch of the deploy operation.
     *
     * If true, then it will be executed once.
     * If false, then the operation will run every time the `operations` command is invoked.
     *
     * @deprecated Will be removed in 7.x version. Use `shouldOnce` method instead.
     */
    protected bool $once = true;

    /**
     * Determines which environment to run on.
     *
     * @deprecated Will be removed in 7.x version. Use `withinEnvironment` method instead.
     */
    protected array|string|null $environment = null;

    /**
     * Determines in which environment it should not run.
     *
     * @deprecated Will be removed in 7.x version. Use `exceptEnvironment` method instead.
     */
    protected array|string|null $exceptEnvironment = null;

    /**
     * Defines a possible "pre-launch" of the operation.
     *
     * @deprecated Will be removed in 7.x version. Use `hasBefore` method instead.
     */
    protected bool $before = true;

    /**
     * @deprecated
     */
    public function getConnection(): ?string
    {
        return config('deploy-operations.connection');
    }

    /**
     * Determines the type of launch of the deploy operation.
     *
     * If true, then it will be executed once.
     * If false, then the operation will run every time the `operations` command is invoked.
     *
     * @deprecated Will be removed in 7.x version. Use `shouldOnce` method instead.
     */
    public function isOnce(): bool
    {
        return $this->once;
    }

    /**
     * Determines the type of launch of the deploy operation.
     *
     * If true, then it will be executed once.
     * If false, then the operation will run every time the `operations` command is invoked.
     */
    public function shouldOnce(): bool
    {
        return $this->isOnce();
    }

    /**
     * Determines a call to database transactions.
     *
     * @deprecated Will be removed in 7.x version. Use `withinTransactions` method instead.
     */
    public function enabledTransactions(): bool
    {
        return (bool) config('deploy-operations.transactions.enabled');
    }

    /**
     * Determines a call to database transactions.
     */
    public function withinTransactions(): bool
    {
        return $this->enabledTransactions();
    }

    /**
     * The number of attempts to execute a request within a transaction before throwing an error.
     *
     * @deprecated Will be removed in 7.x version. Set the value in the `config/deploy-operations.php` settings file.
     */
    public function transactionAttempts(): int
    {
        return config('deploy-operations.transactions.attempts', 1);
    }

    /**
     * Determines which environment to run on.
     *
     * @deprecated Will be removed in 7.x version. Use `withinEnvironment` method instead.
     */
    public function onEnvironment(): array
    {
        return Arr::wrap($this->environment);
    }

    public function withinEnvironment(): bool
    {
        $env = $this->onEnvironment();

        return empty($env) || in_array(app()->environment(), $env, true);
    }

    /**
     * Determines in which environment it should not run.
     *
     * @deprecated Since with version 7.0 will return `bool`.
     */
    public function exceptEnvironment(): array
    {
        return Arr::wrap($this->exceptEnvironment);
    }

    /**
     * Determines whether the given operation can be called conditionally.
     *
     * @deprecated Will be removed in 7.x version. Use `shouldRun` method instead.
     */
    public function allow(): bool
    {
        return true;
    }

    /**
     * Determines whether the given operation can be called conditionally.
     */
    public function shouldRun(): bool
    {
        return $this->allow();
    }

    /**
     * Defines a possible "pre-launch" of the operation.
     *
     * @deprecated Will be removed in 7.x version. Use `needBefore` method instead.
     */
    public function hasBefore(): bool
    {
        return $this->before;
    }

    /**
     * Defines a possible "pre-launch" of the operation.
     */
    public function needBefore(): bool
    {
        return $this->hasBefore();
    }

    /**
     * Defines whether the operation will run synchronously or asynchronously.
     *
     * @deprecated Will be removed in 7.x version. Use `shouldBeAsync` method instead.
     */
    public function isAsync(): bool
    {
        return (bool) config('deploy-operations.async');
    }

    /**
     * Defines whether the operation will run synchronously or asynchronously.
     */
    public function shouldBeAsync(): bool
    {
        return $this->isAsync();
    }

    /**
     * Method to be called when the job completes successfully.
     */
    public function success(): void {}

    /**
     * The method will be called if an error occurs.
     */
    public function failed(): void {}
}
