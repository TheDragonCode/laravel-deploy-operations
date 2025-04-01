<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Listeners;

use DragonCode\LaravelDeployOperations\Console\OperationsCommand;
use DragonCode\LaravelDeployOperations\Console\RollbackCommand;
use DragonCode\LaravelDeployOperations\Constants\Options;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

use function array_merge;

abstract class Listener
{
    protected function withOperation(Migration $migration): ?string
    {
        if (method_exists($migration, 'withOperation')) {
            return $migration->withOperation();
        }

        return null;
    }

    protected function run(string $method, string $operation): void
    {
        match ($method) {
            'up'   => $this->call(OperationsCommand::class, $operation),
            'down' => $this->call(RollbackCommand::class, $operation, ['--force' => true]),
        };
    }

    protected function call(string $command, string $filename, array $parameters = []): void
    {
        Artisan::call($command, array_merge([
            '--' . Options::Path => $filename,
        ], $parameters));
    }
}
