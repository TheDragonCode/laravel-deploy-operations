<?php

namespace DragonCode\LaravelActions\Facades;

use DragonCode\LaravelActions\Support\Information as Support;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string replace(string $value)
 */
class Information extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Support::class;
    }
}
