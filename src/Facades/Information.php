<?php

namespace Helldar\LaravelActions\Facades;

use Helldar\LaravelActions\Support\Information as Support;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string replace(string $value)
 */
final class Information extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Support::class;
    }
}
