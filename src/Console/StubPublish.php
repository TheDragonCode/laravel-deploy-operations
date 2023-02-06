<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Processors\Processor;
use DragonCode\LaravelActions\Processors\StubPublish as StubProcessor;

class StubPublish extends Command
{
    protected $name = Names::STUB_PUBLISH;

    protected $description = 'Publish stub that are available for customization';

    protected Processor|string $processor = StubProcessor::class;

    protected bool $secure = false;

    protected array $options = [
        Options::FORCE,
    ];
}
