<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Rollback as RollbackProcessor;

class Rollback extends Command
{
    protected $name = Names::ROLLBACK;

    protected $description = 'Rollback the last database action';

    protected Processor|string $processor = RollbackProcessor::class;

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::STEP,
    ];
}
