<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Rollback extends Command
{
    protected $name = Names::ROLLBACK;

    protected $description = 'Rollback the last database action';

    protected array $options = [
        Options::CONNECTION,
        Options::FORCE,
        Options::STEP,
    ];
}
