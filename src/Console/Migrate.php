<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\Database;
use DragonCode\LaravelActions\Concerns\Notifications;
use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Constants\Names;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseCommand;

class Migrate extends BaseCommand
{
    use Database;
    use Notifications;
    use Optionable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = Names::MIGRATE
    . ' {--database= : The database connection to use}'
    . ' {--force : Force the operation to run when in production}'
    . ' {--step : Force the actions to be run so they can be rolled back individually}'
    . ' {--path=* : The path(s) to the migrations files to be executed}'
    . ' {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}'
    . ' {--before : Run actions marked as before}';

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
                    'step'   => $this->optionStep(),
                    'before' => $this->optionBefore(),
                ]);
        });

        return 0;
    }

    /**
     * Prepare the action database for running.
     */
    protected function prepareDatabase(): void
    {
        if (! $this->migrator->repositoryExists()) {
            $this->call(
                Names::INSTALL,
                array_filter([
                    '--database' => $this->optionDatabase(),
                ])
            );
        }
    }
}
