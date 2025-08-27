<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Helpers;

use DragonCode\LaravelDeployOperations\Console\OperationsCommand;
use DragonCode\LaravelDeployOperations\Constants\Options;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class OperationHelper
{
    public static function run(?string $path = null, ?bool $realpath = null): void
    {
        $parameters = (new Collection)
            ->when($path, fn (Collection $items) => $items->put('--' . Options::Path, $path))
            ->when($realpath, fn (Collection $items) => $items->put('--' . Options::Realpath, true))
            ->all();

        Artisan::call(OperationsCommand::class, $parameters);
    }
}
