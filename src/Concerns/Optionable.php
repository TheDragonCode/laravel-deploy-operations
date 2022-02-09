<?php

namespace DragonCode\LaravelActions\Concerns;

use Illuminate\Support\Str;

/** @mixin \Illuminate\Console\Command */
trait Optionable
{
    protected function optionDatabase(): ?string
    {
        return $this->input->getOption('database');
    }

    protected function optionStep(?int $default = null): ?int
    {
        return $this->input->getOption('step') ?: $default;
    }

    protected function optionPath(): ?array
    {
        if ($path = $this->option('path')) {
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
