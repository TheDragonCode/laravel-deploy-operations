<?php

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\Database;
use DragonCode\LaravelActions\Concerns\Infoable;
use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Constants\Names;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\DatabaseRefreshed;
use Symfony\Component\Console\Input\InputOption;

class Refresh extends BaseCommand
{
    use ConfirmableTrait;
    use Database;
    use Infoable;
    use Optionable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = Names::REFRESH;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and re-run all actions';

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $database = $this->optionDatabase();
        $step     = $this->optionStep(0);

        $step > 0
            ? $this->runRollback($database, $step)
            : $this->runReset($database);

        $this->call(Names::MIGRATE, array_filter([
            '--database' => $database,
            '--force'    => true,
        ]));

        if ($this->laravel->bound(Dispatcher::class) && class_exists(DatabaseRefreshed::class)) {
            $this->laravel[Dispatcher::class]->dispatch(
                new DatabaseRefreshed()
            );
        }

        return 0;
    }

    /**
     * Run the rollback command.
     *
     * @param string|null $database
     * @param int|null $step
     */
    protected function runRollback(?string $database, ?int $step)
    {
        $this->call(Names::ROLLBACK, array_filter([
            '--database' => $database,
            '--step'     => $step,
            '--force'    => true,
        ]));
    }

    /**
     * Run the reset command.
     *
     * @param string|null $database
     */
    protected function runReset(?string $database)
    {
        $this->call(Names::RESET, array_filter([
            '--database' => $database,
            '--force'    => true,
        ]));
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

            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of actions to be reverted & re-run'],
        ];
    }
}
