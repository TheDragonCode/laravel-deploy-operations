<?php

namespace Tests\Concerns;

use Helldar\LaravelActions\Facades\Version;

trait Laraveable
{
    protected function is6x(): bool
    {
        return Version::is6x();
    }
}
