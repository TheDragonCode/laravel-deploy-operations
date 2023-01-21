<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Actions as ActionsProcessor;
use DragonCode\LaravelActions\Processors\Processor;

class Actions extends Command
{
    protected $signature = Names::ACTIONS;

    protected $description = 'Run the actions';

    protected Processor|string $processor = ActionsProcessor::class;

    protected bool $secure = false;

    protected array $options = [
        Options::BEFORE,
        Options::CONNECTION,
        Options::PATH,
        Options::REALPATH,
        Options::MUTE,
        Options::ISOLATED,
    ];
}
