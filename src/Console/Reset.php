<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Reset as ResetProcessor;

class Reset extends Command
{
    protected $name = Names::RESET;

    protected $description = 'Rollback all database actions';

    protected Processor|string $processor = ResetProcessor::class;

    protected array $options = [
        Options::CONNECTION,
        Options::PATH,
        Options::REALPATH,
        Options::STEP,
        Options::FORCE,
    ];
}
