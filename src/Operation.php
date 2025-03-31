<?php

namespace DragonCode\LaravelDeployOperations;

use DragonCode\LaravelDeployOperations\Concerns\Artisan;

abstract class Operation
{
    use Artisan;

    /**
     * Determines the type of launch of the deploy operation.
     *
     * If true, then it will be executed once.
     * If false, then the operation will run every time the `operations` command is invoked.
     */
    public function shouldOnce(): bool
    {
        return true;
    }

    /**
     * Determines a call to database transactions.
     */
    public function withinTransactions(): bool
    {
        return (bool) config('deploy-operations.transactions.enabled');
    }

    /**
     * Determines whether the given operation can be called conditionally.
     */
    public function shouldRun(): bool
    {
        return true;
    }

    /**
     * Defines a possible "pre-launch" of the operation.
     */
    public function needBefore(): bool
    {
        return true;
    }

    /**
     * Defines whether the operation will run synchronously or asynchronously.
     */
    public function shouldBeAsync(): bool
    {
        return (bool) config('deploy-operations.async');
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
