<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Make as MakeProcessor;
use DragonCode\LaravelActions\Processors\Processor;

class Make extends Command
{
    protected $signature = Names::MAKE;

    protected $description = 'Create a new action file';

    protected Processor|string $processor = MakeProcessor::class;

    protected array $arguments = [
        Options::NAME,
    ];

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
        Options::MUTE,
    ];
}
