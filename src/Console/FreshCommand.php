<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\FreshProcessor;
use DragonCode\LaravelDeployOperations\Processors\Processor;

class FreshCommand extends Command
{
    protected $name = Names::Fresh;

    protected $description = 'Drop and re-run all deploy operations';

    protected Processor|string $processor = FreshProcessor::class;

    protected array $options = [
        Options::Connection,
        Options::Force,
        Options::Path,
        Options::Realpath,
        Options::Mute,
        Options::Isolated,
    ];
}
