<?php

namespace Helldar\LaravelActions\Helpers;

use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class Version
{
    public function is6x(): bool
    {
        return $this->major() === 6;
    }

    protected function major(): int
    {
        return Str::before(Application::VERSION, '.');
    }
}
