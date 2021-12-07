<?php

namespace DragonCode\LaravelActions\Concerns;

/** @mixin \Illuminate\Database\Console\Migrations\BaseCommand */
trait Database
{
    protected function getMigrationPath(): string
    {
        return $this->laravel->databasePath('actions');
    }

    protected function getActionsTable(): string
    {
        return config('database.actions');
    }
}
