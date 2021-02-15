<?php

namespace Helldar\LaravelActions\Support;

use Helldar\LaravelActions\Contracts\Actionable as Contract;
use Illuminate\Database\Migrations\Migration;

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
     * Reverse the actions.
     *
     * @return void
     */
    public function down(): void
    {
        //
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
}
