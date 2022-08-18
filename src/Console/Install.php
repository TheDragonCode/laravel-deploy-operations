<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Processors\Installer;
use DragonCode\LaravelActions\Processors\Processor;

class Install extends Command
{
    protected $name = Names::INSTALL;

    protected $description = 'Create the actions repository';

    protected Processor|string $processor = Installer::class;
}
