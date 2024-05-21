<?php

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Processors\Install as InstallProcessor;
use DragonCode\LaravelDeployOperations\Processors\Processor;

class Install extends Command
{
    protected $name = Names::Install;

    protected $description = 'Create the deploy operations repository';

    protected Processor|string $processor = InstallProcessor::class;
}
