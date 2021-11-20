<?php

namespace DragonCode\LaravelActions\Traits;

/** @mixin \Illuminate\Database\Console\Migrations\BaseCommand */
trait Database
{
    protected function getMigrationPath(): string
    {
        return $this->laravel->databasePath('actions');
    }
}
