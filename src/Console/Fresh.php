<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Fresh as FreshProcessor;
use DragonCode\LaravelActions\Processors\Processor;

class Fresh extends Command
{
    protected $name = Names::FRESH;

    protected $description = 'Drop and re-run all actions';

    protected Processor|string $processor = FreshProcessor::class;

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
        Options::SILENT,
    ];
}
