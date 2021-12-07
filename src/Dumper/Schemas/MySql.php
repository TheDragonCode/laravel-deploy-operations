<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Dumper\Schemas;

use DragonCode\LaravelActions\Concerns\Database;
use Illuminate\Database\Schema\MySqlSchemaState;

class MySql extends MySqlSchemaState
{
    use Database;

    protected function appendMigrationData(string $path)
    {
        $process = $this->executeDumpProcess($this->makeProcess(
            $this->baseDumpCommand() . ' ' . $this->migrationTable . ' ' . $this->getActionsTable()
            . ' --no-create-info --skip-extended-insert --skip-routines --compact'
        ), null, array_merge($this->baseVariables($this->connection->getConfig()), [
            //
        ]));

        $this->files->append($path, $process->getOutput());
    }
}
