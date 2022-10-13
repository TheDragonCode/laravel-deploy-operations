<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Status as StatusProcessor;

class Status extends Command
{
    protected $name = Names::STATUS;

    protected $description = 'Show the status of each action';

    protected Processor|string $processor = StatusProcessor::class;

    protected bool $secure = false;

    protected array $options = [
        Options::CONNECTION,
        Options::PATH,
        Options::REALPATH,
        Options::SILENT,
    ];
}
