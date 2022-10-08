<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Refresh as RefreshProcessor;

class Refresh extends Command
{
    protected $name = Names::REFRESH;

    protected $description = 'Reset and re-run all actions';

    protected Processor|string $processor = RefreshProcessor::class;

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
    ];
}
