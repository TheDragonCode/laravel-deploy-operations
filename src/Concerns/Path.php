<?php

namespace DragonCode\LaravelActions\Concerns;

trait Path
{
    protected function getActionsPath(string $path = DIRECTORY_SEPARATOR): string
    {
        return base_path('actions' . DIRECTORY_SEPARATOR . ltrim($path, '\\/'));
    }
}
