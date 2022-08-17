<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Constants\Options;
use Illuminate\Support\Str;

/** @mixin \Illuminate\Console\Command */
trait Optionable
{
    protected function optionForce(): bool
    {
        return $this->hasOption(Options::FORCE) && $this->option(Options::FORCE);
    }

    protected function optionBefore(): bool
    {
        return $this->option(Options::BEFORE);
    }

    protected function optionDatabase(): ?string
    {
        return $this->option(Options::DATABASE);
    }

    protected function optionStep(?int $default = null): ?int
    {
        return $this->option(Options::STEP) ?: $default;
    }

    protected function optionPath(): ?array
    {
        if (! $this->hasOption(Options::PATH)) {
            return null;
        }

        if ($path = $this->option(Options::PATH)) {
            return collect($path)->map(function ($path) {
                if ($this->usingRealPath()) {
                    return $path;
                }

                $filename = $this->getMigrationPath() . DIRECTORY_SEPARATOR . $path;

                if (is_dir($filename) || file_exists($filename)) {
                    return $filename;
                }

                return Str::finish($filename, '.php');
            })->all();
        }

        return null;
    }
}
