<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Helpers;

class Config
{
    public function connection(): ?string
    {
        return config('database.actions.connection');
    }

    public function table(): string
    {
        return config('database.actions.table');
    }

    public function path(?string $path = null): string
    {
        $directory = config('database.actions.path', base_path('actions'));

        return rtrim($directory, '\\/') . DIRECTORY_SEPARATOR . ltrim((string) $path, '\\/');
    }

    public function gitPath(): string
    {
        return base_path();
    }
}
