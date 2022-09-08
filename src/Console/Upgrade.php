<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Processors\Install as InstallProcessor;
use DragonCode\LaravelActions\Processors\Processor;

class Upgrade extends Command
{
    protected $name = Names::UPGRADE;

    protected $description = 'Action structure upgrade from version 2 to 3';

    protected Processor|string $processor = InstallProcessor::class;
}
