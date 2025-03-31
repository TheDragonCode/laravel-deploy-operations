<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\Processor;
use DragonCode\LaravelDeployOperations\Processors\Refresh as RefreshProcessor;

class Refresh extends Command
{
    protected $name = Names::Refresh;

    protected $description = 'Reset and re-run all deploy operations';

    protected Processor|string $processor = RefreshProcessor::class;

    protected array $options = [
        Options::Connection,
        Options::Force,
        Options::Path,
        Options::Realpath,
        Options::Mute,
        Options::Isolated,
    ];
}
