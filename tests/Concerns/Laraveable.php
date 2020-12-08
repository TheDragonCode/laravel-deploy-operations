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

    protected function is7x(): bool
    {
        return $this->majorVersion() === 7;
    }

    protected function is8x(): bool
    {
        return $this->majorVersion() === 8;
    }

    protected function majorVersion(): int
    {
        return Str::before(Application::VERSION, '.');
    }
}
