<?php

namespace Helldar\LaravelActions\Support;

use Helldar\LaravelActions\Contracts\Actionable as Contract;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;

abstract class Actionable extends Migration implements Contract
{
    /**
     * Determines the type of launch of the action.
     *
     * If true, then it will be executed once.
     * If false, then the action will run every time the `migrate:actions` command is invoked.
     *
     * @var bool
     */
    protected $once = true;

    /**
     * Determines a call to database transactions.
     *
     * By default, false.
     *
     * @var bool
     */
    protected $transactions = false;

    /**
     * The number of attempts to execute a request within a transaction before throwing an error.
     *
     * @var int
     */
    protected $transaction_attempts = 1;

    /**
     * Determines which environment to run on.
     *
     * @var array|string|null
     */
    protected $environment = null;

    /**
     * Determines in which environment it should not run.
     *
     * @var array|string|null
     */
    protected $except_environment = null;

    /**
     * Reverse the actions.
     */
    public function down(): void
    {
    }

    /**
     * Determines the type of launch of the action.
     *
     * If true, then it will be executed once.
     * If false, then the action will run every time the `migrate:actions` command is invoked.
     *
     * @return bool
     */
    public function isOnce(): bool
    {
        return $this->once;
    }

    /**
     * Determines a call to database transactions.
     *
     * @return bool
     */
    public function enabledTransactions(): bool
    {
        return $this->transactions;
    }

    /**
     * The number of attempts to execute a request within a transaction before throwing an error.
     *
     * @return int
     */
    public function transactionAttempts(): int
    {
        return $this->transaction_attempts;
    }

    /**
     * Determines which environment to run on.
     *
     * @return array
     */
    public function onEnvironment(): array
    {
        return Arr::wrap($this->environment);
    }

    /**
     * Determines in which environment it should not run.
     *
     * @return array
     */
    public function exceptEnvironment(): array
    {
        return Arr::wrap($this->except_environment);
    }

    /**
     * Determines whether the given action can be called conditionally.
     *
     * @return bool
     */
    public function allow(): bool
    {
        return true;
    }
}
