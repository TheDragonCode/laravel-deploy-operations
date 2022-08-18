<?php

namespace DragonCode\LaravelActions\Concerns;

trait Database
{
    protected function getTableName(): string
    {
        return config('database.actions', 'migration_actions');
    }
}
