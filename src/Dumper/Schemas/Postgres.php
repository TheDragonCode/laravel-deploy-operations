<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Dumper\Schemas;

use DragonCode\LaravelActions\Concerns\Database;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\PostgresSchemaState;

class Postgres extends PostgresSchemaState
{
    use Database;

    public function dump(Connection $connection, $path)
    {
        $excluded = collect($connection->getSchemaBuilder()->getAllTables())
            ->map->tablename
            ->reject(function ($table) {
                return $table === $this->migrationTable;
            })->map(function ($table) {
                return '--exclude-table-data="*.' . $table . '"';
            })->implode(' ');

        $this->makeProcess(
            $this->baseDumpCommand() . ' --file="${:LARAVEL_LOAD_PATH}" ' . $excluded
        )->mustRun($this->output, array_merge($this->baseVariables($this->connection->getConfig()), [
            'LARAVEL_LOAD_PATH' => $path,
        ]));
    }
}
