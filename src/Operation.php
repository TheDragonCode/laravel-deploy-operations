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
     */
    protected bool $once = true;

    /**
     * Determines a call to database transactions.
     *
     * By default, false.
     */
    protected bool $transactions = false;

    /** The number of attempts to execute a request within a transaction before throwing an error. */
    protected int $transactionAttempts = 1;

    /**
     * Determines which environment to run on.
     */
    protected array|string|null $environment = null;

    /**
     * Determines in which environment it should not run.
     */
    protected array|string|null $exceptEnvironment = null;

    /** Defines a possible "pre-launch" of the operation. */
    protected bool $before = true;

    /** Defines whether the operation will run synchronously or asynchronously. */
    protected bool $async = false;

    public function getConnection(): ?string
    {
        return config('deploy-operations.connection');
    }

    /**
     * Determines the type of launch of the deploy operation.
     *
     * If true, then it will be executed once.
     * If false, then the operation will run every time the `operations` command is invoked.
     */
    public function isOnce(): bool
    {
        return $this->once;
    }

    /**
     * Determines a call to database transactions.
     */
    public function enabledTransactions(): bool
    {
        return $this->transactions;
    }

    /**
     * The number of attempts to execute a request within a transaction before throwing an error.
     */
    public function transactionAttempts(): int
    {
        return $this->transactionAttempts;
    }

    /**
     * Determines which environment to run on.
     */
    public function onEnvironment(): array
    {
        return Arr::wrap($this->environment);
    }

    /**
     * Determines in which environment it should not run.
     */
    public function exceptEnvironment(): array
    {
        return Arr::wrap($this->exceptEnvironment);
    }

    /**
     * Determines whether the given operation can be called conditionally.
     */
    public function allow(): bool
    {
        return true;
    }

    /**
     * Defines a possible "pre-launch" of the operation.
     */
    public function hasBefore(): bool
    {
        return $this->before;
    }

    /**
     * Defines whether the operation will run synchronously or asynchronously.
     */
    public function isAsync(): bool
    {
        return $this->async;
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
