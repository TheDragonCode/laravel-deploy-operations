<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Helpers;

use DragonCode\Support\Facades\Helpers\Arr;

use function base_path;
use function config as appConfig;
use function ltrim;
use function rtrim;
use function str_replace;

class Config
{
    public function environment(): ?string
    {
        return appConfig('app.env', 'production');
    }

    public function connection(): ?string
    {
        return appConfig('actions.connection');
    }

    public function table(): string
    {
        return appConfig('actions.table');
    }

    public function exclude(): array
    {
        return Arr::of((array) appConfig('actions.exclude'))
            ->map(fn (string $path) => str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path))
            ->filter()
            ->toArray();
    }

    public function path(?string $path = null): string
    {
        return rtrim($this->directory(), '\\/') . DIRECTORY_SEPARATOR . ltrim((string) $path, '\\/');
    }

    public function gitPath(): string
    {
        return base_path();
    }

    protected function directory(): string
    {
        return appConfig('actions.path', base_path('actions'));
    }
}
