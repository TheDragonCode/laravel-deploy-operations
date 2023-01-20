<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Rollback as RollbackProcessor;

class Rollback extends Command
{
    protected $name = Names::ROLLBACK;

    protected $description = 'Rollback the last action';

    protected Processor|string $processor = RollbackProcessor::class;

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
        Options::STEP,
        Options::MUTE,
        Options::ISOLATED,
    ];
}
