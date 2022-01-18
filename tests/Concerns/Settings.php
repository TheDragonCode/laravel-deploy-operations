<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Config as AppConfig;

trait Settings
{
    protected $table = 'foo_actions';

    protected function setTable($app): void
    {
        $app['config']->set('database.actions', $this->table);
    }

    protected function setCache(bool $value): void
    {
        AppConfig::set('database.actions_cache.config', $value);
        AppConfig::set('database.actions_cache.route', $value);
        AppConfig::set('database.actions_cache.view', $value);
        AppConfig::set('database.actions_cache.event', $value);
    }

    protected function setRestart(bool $value): void
    {
        AppConfig::set('database.actions_daemons.queue', $value);
        AppConfig::set('database.actions_daemons.horizon', $value);
        AppConfig::set('database.actions_daemons.octane', $value);
    }
}
