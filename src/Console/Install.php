<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Traits\Database;
use DragonCode\LaravelActions\Traits\Infoable;
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
