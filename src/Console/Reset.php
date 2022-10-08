<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Reset as ResetProcessor;

class Reset extends Command
{
    protected $name = Names::RESET;

    protected $description = 'Rollback all actions';

    protected Processor|string $processor = rollback::class;

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
    ];
}
