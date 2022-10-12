<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\Upgrade as UpgradeProcessor;

class Upgrade extends Command
{
    protected $name = Names::UPGRADE;

    protected $description = 'Action structure upgrade from version 2 to 3';

    protected Processor|string $processor = UpgradeProcessor::class;
}
