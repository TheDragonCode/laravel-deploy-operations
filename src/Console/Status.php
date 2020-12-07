<?php

namespace Helldar\LaravelActions\Console;

use Helldar\LaravelActions\Constants\Names;
use Helldar\LaravelActions\Traits\Database;
use Illuminate\Database\Console\Migrations\StatusCommand as BaseCommand;

final class Status extends BaseCommand
{
    use Database;

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
