<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\OperationsProcessor;
use DragonCode\LaravelDeployOperations\Processors\Processor;

class OperationsCommand extends Command
{
    protected $signature = Names::Operations;

    protected $description = 'Run the deploy operations';

    protected Processor|string $processor = OperationsProcessor::class;

    protected bool $secure = false;

    protected array $options = [
        Options::Before,
        Options::Connection,
        Options::Path,
        Options::Realpath,
        Options::Mute,
        Options::Isolated,
        Options::Sync,
    ];
}
