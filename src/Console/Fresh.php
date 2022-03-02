<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Constants\Names;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Support\Facades\Schema;

class Fresh extends FreshCommand
{
    use Optionable;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = Names::FRESH;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop and re-run all actions';

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $database = $this->optionDatabase();

        $this->dropTable();
        $this->migrate($database);

        return 0;
    }

    protected function dropTable(): void
    {
        Schema::dropIfExists($this->getTableName());
    }

    protected function migrate(?string $database): void
    {
        $this->call(
            Names::MIGRATE,
            array_filter([
                '--database' => $database,
                '--force'    => true,
            ])
        );
    }

    protected function getTableName(): string
    {
        return config('database.actions');
    }
}
