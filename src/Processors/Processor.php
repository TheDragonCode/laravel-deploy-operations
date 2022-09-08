<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Concerns\Artisan;
use DragonCode\LaravelActions\Concerns\Database;
use DragonCode\LaravelActions\Concerns\Notifications;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Values\Options;
use Illuminate\Filesystem\Filesystem;

abstract class Processor
{
    use Artisan;
    use Database;
    use Notifications;

    abstract public function handle();

    public function __construct(
        protected Options $options,
        protected Filesystem $files,
        protected ActionRepository $repository
    ) {
    }
}
