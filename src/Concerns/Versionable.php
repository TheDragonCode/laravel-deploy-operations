<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelSupport\Facades\AppVersion;

trait Versionable
{
    protected function isLatestApp(): bool
    {
        return AppVersion::is9x();
    }

    protected function isPrevApp(): bool
    {
        return ! $this->isLatestApp();
    }
}
