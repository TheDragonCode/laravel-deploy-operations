<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Dumper\Schemas;

use DragonCode\LaravelActions\Concerns\Database;
use Illuminate\Database\Schema\SqliteSchemaState;

class SQLite extends SqliteSchemaState
{
    use Database;

    protected function appendMigrationData(string $path)
    {
        with($process = $this->makeProcess(
            $this->baseCommand() . ' ".dump \'' . $this->migrationTable . ' ' . '\'' . $this->getActionsTable() . '\'' . '\'"'
        ))->mustRun(null, $this->baseVariables($this->connection->getConfig()));

        $migrations = collect(preg_split("/\r\n|\n|\r/", $process->getOutput()))->filter(function ($line) {
            return preg_match('/^\s*(--|INSERT\s)/iu', $line) === 1 &&
                strlen($line) > 0;
        })->all();

        $this->files->append($path, implode(PHP_EOL, $migrations) . PHP_EOL);
    }
}
