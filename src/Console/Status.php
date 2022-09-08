<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use Illuminate\Database\Console\Migrations\StatusCommand as BaseCommand;

class Status extends BaseCommand
{
    protected $name = Names::STATUS;

    protected $description = 'Show the status of each action';

    protected array $options = [
        Options::CONNECTION,
        Options::BEFORE,
    ];
}
