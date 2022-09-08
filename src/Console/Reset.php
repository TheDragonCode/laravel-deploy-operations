<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;

class Reset extends Command
{
    protected $name = Names::RESET;

    protected $description = 'Rollback all database actions';
}
