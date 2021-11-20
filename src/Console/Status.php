<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Traits\Database;
use DragonCode\LaravelActions\Traits\Infoable;
use Illuminate\Database\Console\Migrations\StatusCommand as BaseCommand;

class Status extends BaseCommand
{
    use Database;
    use Infoable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = Names::STATUS;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of each action';
}
