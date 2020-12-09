<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

trait Laraveable
{
    protected function is6x(): bool
    {
        return $this->majorVersion() === 6;
    }

    protected function majorVersion(): int
    {
        return Str::before(Application::VERSION, '.');
    }
}
