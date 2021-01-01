<?php

namespace Helldar\LaravelActions\Support;

use Helldar\LaravelActions\Contracts\Actionable as Contract;
use Illuminate\Database\Migrations\Migration;

abstract class Actionable extends Migration implements Contract
{
    protected $once = true;

    public function isOnce(): bool
    {
        return $this->once;
    }
}
