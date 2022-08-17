<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Console;

use DragonCode\LaravelActions\Concerns\Optionable;
use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use Illuminate\Support\Facades\Schema;

class Fresh extends Command
{
    use Optionable;

    protected $name = Names::FRESH;

    protected $description = 'Drop and re-run all actions';

    public function process(): void
    {
        $this->drop();
        $this->migrate();
    }

    protected function drop(): void
    {
        Schema::dropIfExists($this->getTableName());
    }

    protected function migrate(): void
    {
        $this->call(Names::MIGRATE,
            array_filter([
                '--' . Options::DATABASE => $this->optionDatabase(),
                '--' . Options::FORCE    => true,
            ]));
    }

    protected function getTableName(): string
    {
        return config('database.actions');
    }
}
