<?php

namespace Tests\Concerns;

trait Path
{
    protected function actionsPath(string $path = '/'): string
    {
        return base_path('actions/' . ltrim($path, '/'));
    }
}
