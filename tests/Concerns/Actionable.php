<?php

namespace Tests\Concerns;

use DragonCode\LaravelSupport\Facades\AppVersion;

trait Actionable
{
    protected function getMigrationPath(): string
    {
        return AppVersion::is9x()
            ? __DIR__ . '/../fixtures/app/9.x/actions'
            : __DIR__ . '/../fixtures/app/prev/actions';
    }
}
