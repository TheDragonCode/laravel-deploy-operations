<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\Processor;
use DragonCode\LaravelDeployOperations\Processors\Reset as ResetProcessor;

class Reset extends Command
{
    protected $name = Names::Reset;

    protected $description = 'Rollback all deploy operations';

    protected Processor|string $processor = ResetProcessor::class;

    protected array $options = [
        Options::Connection,
        Options::Force,
        Options::Path,
        Options::Realpath,
        Options::Mute,
        Options::Isolated,
    ];
}
