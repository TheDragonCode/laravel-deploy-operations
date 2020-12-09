<?php

namespace Helldar\LaravelActions\Console;

use Helldar\LaravelActions\Constants\Names;
use Helldar\LaravelActions\Traits\Database;
use Helldar\LaravelActions\Traits\Infoable;
use Illuminate\Database\Console\Migrations\InstallCommand as BaseCommand;

final class Install extends BaseCommand
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
