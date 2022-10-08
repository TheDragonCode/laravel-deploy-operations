<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Install as InstallProcessor;
use DragonCode\LaravelActions\Processors\Processor;

class Install extends Command
{
    protected $name = Names::INSTALL;

    protected $description = 'Create the actions repository';

    protected Processor|string $processor = InstallProcessor::class;

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
    ];
}
