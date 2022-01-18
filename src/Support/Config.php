<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Support;

use DragonCode\LaravelActions\Config\Cache;
use DragonCode\LaravelActions\Config\Queue;
use Illuminate\Support\Facades\Config as AppConfig;

class Config
{
    public function cache(): Cache
    {
        $values = AppConfig::get('database.actions_cache', []);

        return Cache::make($values);
    }

    public function queue(): Queue
    {
        $values = AppConfig::get('database.actions_daemons', []);

        return Queue::make($values);
    }
}
