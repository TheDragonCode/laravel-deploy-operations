<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelSupport\Facades\AppVersion;

trait Anonymous
{
    protected function allowAnonymousMigrations(): bool
    {
        return AppVersion::is('8.37');
    }
}
