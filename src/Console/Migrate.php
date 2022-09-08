<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Migrate extends Command
{
    protected $signature = Names::MIGRATE;

    protected $description = 'Run the actions';

    protected array $options = [
        Options::BEFORE,
        Options::CONNECTION,
        Options::FORCE,
        Options::PATH,
        Options::REALPATH,
        Options::STEP,
    ];
}
