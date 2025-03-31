<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Processors\InstallProcessor;
use DragonCode\LaravelDeployOperations\Processors\Processor;

class InstallCommand extends Command
{
    protected $name = Names::Install;

    protected $description = 'Create the deploy operations repository';

    protected Processor|string $processor = InstallProcessor::class;
}
