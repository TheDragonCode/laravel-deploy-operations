<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\Processor;
use DragonCode\LaravelDeployOperations\Processors\Rollback as RollbackProcessor;

class Rollback extends Command
{
    protected $name = Names::Rollback;

    protected $description = 'Rollback the last deploy operation';

    protected Processor|string $processor = RollbackProcessor::class;

    protected array $options = [
        Options::Connection,
        Options::Force,
        Options::Path,
        Options::Realpath,
        Options::Step,
        Options::Mute,
        Options::Isolated,
    ];
}
