<?php

namespace DragonCode\LaravelDeployOperations\Console;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Processors\Processor;
use DragonCode\LaravelDeployOperations\Processors\Status as StatusProcessor;

class Status extends Command
{
    protected $name = Names::Status;

    protected $description = 'Show the status of each deploy operation';

    protected Processor|string $processor = StatusProcessor::class;

    protected bool $secure = false;

    protected array $options = [
        Options::Connection,
        Options::Path,
        Options::Realpath,
        Options::Mute,
    ];
}
