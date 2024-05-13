<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Helpers;

use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Config\Repository;

use function base_path;
use function ltrim;
use function rtrim;
use function str_replace;

class Config
{
    public function __construct(
        protected Repository $config
    ) {}

    public function environment(): ?string
    {
        return $this->config->get('app.env', 'production');
    }

    public function connection(): ?string
    {
        return $this->config->get('actions.connection');
    }

    public function table(): string
    {
        return $this->config->get('actions.table');
    }

    public function exclude(): array
    {
        return Arr::of((array) $this->config->get('actions.exclude'))
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
        return $this->config->get('actions.path', base_path('actions'));
    }
}
