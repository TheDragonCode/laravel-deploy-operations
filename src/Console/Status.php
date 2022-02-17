<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\Database;
use DragonCode\LaravelActions\Concerns\Infoable;
use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Constants\Names;
use Illuminate\Database\Console\Migrations\StatusCommand as BaseCommand;

class Status extends BaseCommand
{
    use Database;
    use Infoable;
    use Optionable;

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
