<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Upgrade as UpgradeProcessor;

class Upgrade extends Command
{
    protected $name = Names::UPGRADE;

    protected $description = 'Action project upgrade from version 3 to 4';

    protected Processor|string $processor = UpgradeProcessor::class;
}
