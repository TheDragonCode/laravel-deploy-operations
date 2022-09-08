<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Refresh extends Command
{
    protected $name = Names::REFRESH;

    protected $description = 'Reset and re-run all actions';

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::STEP,
    ];
}
