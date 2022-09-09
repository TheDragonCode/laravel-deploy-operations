<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Migrate as MigrateProcessor;
use DragonCode\LaravelActions\Processors\Processor;

class Migrate extends Command
{
    protected $signature = Names::MIGRATE;

    protected $description = 'Run the actions';

    protected Processor|string $processor = MigrateProcessor::class;

    protected array $options = [
        Options::BEFORE,
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
        Options::STEP,
    ];
}
