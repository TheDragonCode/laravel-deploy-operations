<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\MakeProcessor;
use DragonCode\LaravelDeployOperations\Processors\Processor;

class MakeCommand extends Command
{
    protected $signature = Names::Make;

    protected $description = 'Create a new deploy operation file';

    protected Processor|string $processor = MakeProcessor::class;

    protected array $arguments = [
        Options::Name,
    ];

    protected array $options = [
        Options::Connection,
        Options::Force,
        Options::Path,
        Options::Realpath,
        Options::Mute,
    ];
}
