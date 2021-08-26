<?php

namespace Helldar\LaravelActions\Console;

use Helldar\LaravelActions\Constants\Names;
use Helldar\LaravelActions\Traits\Database;
use Helldar\LaravelActions\Traits\Infoable;
use Helldar\LaravelActions\Traits\Optionable;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseCommand;

class Migrate extends BaseCommand
{
    use Database;
    use Infoable;
    use Optionable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = Names::MIGRATE
    . ' {--database= : The database connection to use}'
    . ' {--force : Force the operation to run when in production}'
    . ' {--step : Force the actions to be run so they can be rolled back individually}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the actions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $this->migrator->usingConnection($this->optionDatabase(), function () {
            $this->prepareDatabase();

            $this->migrator->setOutput($this->output)
                ->run($this->getMigrationPaths(), [
                    'pretend' => null,
                    'step'    => $this->optionStep(),
                ]);
        });

        return 0;
    }

    /**
     * Prepare the action database for running.
     */
    protected function prepareDatabase()
    {
        if (! $this->migrator->repositoryExists()) {
            $this->call(Names::INSTALL, array_filter([
                '--database' => $this->optionDatabase(),
            ]));
        }
    }
}
