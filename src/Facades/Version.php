<?php

namespace Helldar\LaravelActions\Facades;

use Helldar\LaravelActions\Helpers\Version as Helper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool is6x()
 */
class Version extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Helper::class;
    }
}
