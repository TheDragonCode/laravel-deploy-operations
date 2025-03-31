<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Concerns;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Artisan;

trait HasArtisan
{
    protected function artisan(string $command, array $parameters = [], ?OutputStyle $outputBuffer = null): void
    {
        Artisan::call($command, $parameters, $outputBuffer);
    }
}
