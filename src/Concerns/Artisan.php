<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Artisan as Command;

trait Artisan
{
    protected function artisan(string $command, array $parameters = [], ?OutputStyle $outputBuffer = null): void
    {
        Command::call($command, $parameters, $outputBuffer);
    }
}
