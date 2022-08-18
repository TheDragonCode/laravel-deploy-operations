<?php

namespace DragonCode\LaravelActions\Concerns;

trait Database
{
    protected function table(): string
    {
        return config('database.actions', 'migration_actions');
    }
}
