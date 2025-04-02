<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations;

use DragonCode\LaravelDeployOperations\Concerns\HasArtisan;
use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;

use function app;

abstract class Operation
{
    use HasArtisan;

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
        return app(ConfigData::class)->transactions->enabled;
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
    public function needAsync(): bool
    {
        return app(ConfigData::class)->async;
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
