<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Facades;

use DragonCode\LaravelActions\Support\Git as Support;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null currentBranch(?string $path)
 */
class Git extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Support::class;
    }
}
