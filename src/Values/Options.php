<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Values;

use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\SimpleDataTransferObject\DataTransferObject;
use DragonCode\Support\Facades\Helpers\Boolean;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Container\Container;

class Options extends DataTransferObject
{
    public bool $before = false;

    public ?string $connection = null;

    public bool $force = false;

    public ?string $name = null;

    public ?string $path = null;

    public bool $realpath = false;

    public ?int $step = null;

    public bool $mute = false;

    public bool $sync = false;

    public function resolvePath(): self
    {
        $this->path = $this->realpath
            ? $this->path ?: $this->config()->path()
            : $this->config()->path($this->path);

        return $this;
    }

    protected function castName(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return Str::of($value)
            ->replace('\\', '/')
            ->replace('.php', '')
            ->explode('/')
            ->map(fn (string $path) => Str::snake($path))
            ->implode(DIRECTORY_SEPARATOR)
            ->toString();
    }

    protected function castBefore(mixed $value): bool
    {
        return Boolean::parse($value);
    }

    protected function castForce(mixed $value): bool
    {
        return Boolean::parse($value);
    }

    protected function castRealpath(mixed $value): bool
    {
        return Boolean::parse($value);
    }

    protected function castStep(int|string|null $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    protected function config(): Config
    {
        return Container::getInstance()->make(Config::class);
    }
}
