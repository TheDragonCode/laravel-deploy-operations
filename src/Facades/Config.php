<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Facades;

use DragonCode\LaravelActions\Config\Cache;
use DragonCode\LaravelActions\Config\Queue;
use DragonCode\LaravelActions\Support\Config as Support;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Cache cache()
 * @method static Queue queue()
 */
class Config extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Support::class;
    }
}
