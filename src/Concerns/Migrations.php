<?php

namespace DragonCode\LaravelActions\Concerns;

trait Migrations
{
    protected function table(): string
    {
        return config('database.actions', 'migration_actions');
    }
}
