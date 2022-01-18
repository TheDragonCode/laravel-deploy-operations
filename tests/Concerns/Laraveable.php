<?php

namespace Tests\Concerns;

use DragonCode\LaravelSupport\Facades\AppVersion;

trait Laraveable
{
    protected function is6x(): bool
    {
        return AppVersion::is6x();
    }
}
