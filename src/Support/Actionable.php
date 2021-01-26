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
}
