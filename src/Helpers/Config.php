<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Helpers;

use Illuminate\Config\Repository;

class Config
{
    public function __construct(
        protected Repository $config
    ) {
    }

    public function environment(): ?string
    {
        return $this->config->get('app.env', 'production');
    }

    public function connection(): ?string
    {
        return $this->config->get('database.actions.connection');
    }

    public function table(): string
    {
        return $this->config->get('database.actions.table');
    }

    public function path(?string $path = null): string
    {
        $directory = $this->config->get('database.actions.path', base_path('actions'));

        return rtrim($directory, '\\/') . DIRECTORY_SEPARATOR . ltrim((string) $path, '\\/');
    }

    public function gitPath(): string
    {
        return base_path();
    }
}
