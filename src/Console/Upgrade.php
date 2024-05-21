<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Processors\Processor;
use DragonCode\LaravelDeployOperations\Processors\Upgrade as UpgradeProcessor;

class Upgrade extends Command
{
    protected $name = Names::Upgrade;

    protected $description = 'Upgrading project files from version 5 to 6';

    protected Processor|string $processor = UpgradeProcessor::class;
}
