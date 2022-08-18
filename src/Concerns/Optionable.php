<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Values\Options as OptionsDto;
use Illuminate\Support\Str;

/** @mixin \Illuminate\Console\Command */
trait Optionable
{
    protected function optionForce(): bool
    {
        return $this->optionHas(Options::FORCE);
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

                $filename = $this->getActionsPath($path);

                if (is_dir($filename) || file_exists($filename)) {
                    return $filename;
                }

                return Str::finish($filename, '.php');
            })->all();
        }

        return null;
    }

    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
     *
     * @return bool
     */
    protected function usingRealPath(): bool
    {
        return $this->optionHas(Options::REALPATH);
    }

    protected function optionHas(string $key): bool
    {
        return $this->hasOption($key) && $this->option($key);
    }

    protected function optionDto(): OptionsDto
    {
        return OptionsDto::make([
            'before'   => $this->optionBefore(),
            'database' => $this->optionDatabase(),
            'force'    => $this->optionForce(),
            'path'     => $this->optionPath(),
            'realpath' => $this->usingRealPath(),
            'step'     => $this->optionStep(),
        ]);
    }
}
