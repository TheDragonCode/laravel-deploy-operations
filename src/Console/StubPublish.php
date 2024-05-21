<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\Processor;
use DragonCode\LaravelDeployOperations\Processors\StubPublish as StubProcessor;

class StubPublish extends Command
{
    protected $name = Names::StubPublish;

    protected $description = 'Publish stub that are available for customization';

    protected Processor|string $processor = StubProcessor::class;

    protected bool $secure = false;

    protected array $options = [
        Options::Force,
    ];
}
