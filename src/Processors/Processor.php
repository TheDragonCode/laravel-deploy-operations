<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Concerns\Artisan;
use DragonCode\LaravelActions\Concerns\Database;
use DragonCode\LaravelActions\Values\Options;
use Illuminate\Filesystem\Filesystem;

abstract class Processor
{
    use Artisan;
    use Database;

    public function __construct(
        protected Filesystem $files,
        protected Options    $options
    ) {
    }

    abstract public function handle();
}
