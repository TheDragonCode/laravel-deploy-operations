<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\Database;
use DragonCode\LaravelActions\Concerns\Infoable;
use DragonCode\LaravelActions\Constants\Names;
use Illuminate\Database\Console\Migrations\InstallCommand as BaseCommand;

class Install extends BaseCommand
{
    use Database;
    use Infoable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = Names::INSTALL;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the actions repository';
}
