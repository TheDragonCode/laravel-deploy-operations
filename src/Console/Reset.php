<?php

namespace Helldar\LaravelActions\Console;

use Helldar\LaravelActions\Constants\Names;
use Helldar\LaravelActions\Traits\Database;
use Helldar\LaravelActions\Traits\Optionable;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputOption;

final class Reset extends BaseCommand
{
    use ConfirmableTrait;
    use Database;
    use Optionable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = Names::RESET;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all database actions';

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Create a new migration rollback command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        return $this->migrator->usingConnection($this->optionDatabase(), function () {
            if (! $this->migrator->repositoryExists()) {
                return $this->comment('Migration table not found.');
            }

            $this->migrator->setOutput($this->output)->reset(
                $this->getMigrationPaths()
            );
        });
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
        ];
    }
}
