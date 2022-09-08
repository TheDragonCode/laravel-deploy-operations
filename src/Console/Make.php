<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Processors\Make as MakeProcessor;
use DragonCode\LaravelActions\Processors\Processor;

class Make extends Command
{
    protected $signature = Names::MAKE
    . ' {name? : The name of the action}';

    protected $description = 'Create a new action file';

    protected Processor|string $processor = MakeProcessor::class;
}
