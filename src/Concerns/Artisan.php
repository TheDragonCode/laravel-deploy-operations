<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Illuminate\Support\Facades\Artisan as ArtisanSupport;

trait Artisan
{
    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     *
     * @return void
     */
    protected function artisan(string $command, array $parameters = []): void
    {
        ArtisanSupport::call($command, $parameters);
    }
}
