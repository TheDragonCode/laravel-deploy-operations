<?php

namespace Helldar\LaravelActions\Console;

use Helldar\LaravelActions\Constants\Names;
use Helldar\LaravelActions\Traits\Database;
use Helldar\LaravelActions\Traits\Optionable;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Symfony\Component\Console\Input\InputOption;

final class Rollback extends RollbackCommand
{
    use Database;
    use Optionable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = Names::ROLLBACK;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last database action';

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
            $this->migrator->setOutput($this->output)->rollback(
                $this->getMigrationPaths(),
                [
                    'step' => $this->optionStep(),
                ]
            );
        });

        return 0;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],

            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted'],
        ];
    }
}
