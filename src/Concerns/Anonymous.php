<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelSupport\Facades\AppVersion;

trait Anonymous
{
    protected function allowAnonymous(): bool
    {
        return AppVersion::is('8.37.0');
    }

    protected function disallowAnonymous(): bool
    {
        return ! $this->allowAnonymous();
    }
}
