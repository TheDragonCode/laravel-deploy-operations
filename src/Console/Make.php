<?php

namespace Helldar\LaravelActions\Console;

use Helldar\LaravelActions\Constants\Names;
use Helldar\LaravelActions\Traits\Database;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

final class Make extends BaseCommand
{
    use Database;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = Names::MAKE
    . ' {name : The name of the action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action file';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new action install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @param  \Illuminate\Support\Composer  $composer
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator  = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->writeMigration(
            Str::snake(trim($this->argument('name')))
        );

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the action file to disk.
     *
     * @param  string  $name
     *
     * @throws \Exception
     */
    protected function writeMigration(string $name)
    {
        $file = $this->creator->create(
            $name,
            $this->getMigrationPath()
        );

        $path = pathinfo($file, PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> {$path}");
    }
}
