<?php

namespace DragonCode\LaravelActions\Concerns;

/** @mixin \Illuminate\Database\Console\Migrations\BaseCommand */
trait Database
{
    protected function getMigrationPath(): string
    {
        return $this->laravel->databasePath('actions');
    }

    protected function getMigrationPaths(): array
    {
        if ($paths = $this->optionPath()) {
            return $paths;
        }

        return [$this->getMigrationPath()];
    }
}
