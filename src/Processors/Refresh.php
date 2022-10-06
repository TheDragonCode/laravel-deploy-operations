<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Refresh extends Processor
{
    public function handle(): void
    {
        $connection = $this->options->connection;
        $path     = $this->options->path;
        $realPath = $this->options->realpath;
        $step     = $this->options->step;

        $this->rollback($connection, $path, $realPath, $step);
        $this->runMigrate($connection, $path, $realPath);
    }

    protected function rollback(?string $connection, array $path, bool $realPath, ?int $step): void
    {
        (int) $step > 0
            ? $this->runRollback($connection, $path, $realPath, $step)
            : $this->runReset($connection, $path, $realPath);
    }

    protected function runRollback(?string $connection, array $path, bool $realPath, ?int $step): void
    {
        $this->runCommand(Names::ROLLBACK, [
            '--' . Options::CONNECTION => $connection,
            '--' . Options::PATH       => $path,
            '--' . Options::REALPATH   => $realPath,
            '--' . Options::STEP       => $step,
            '--' . Options::FORCE      => true,
        ]);
    }

    protected function runReset(?string $connection, array $path, bool $realPath): void
    {
        $this->runCommand(Names::RESET, [
            '--' . Options::CONNECTION => $connection,
            '--' . Options::PATH       => $path,
            '--' . Options::REALPATH   => $realPath,
            '--' . Options::FORCE      => true,
        ]);
    }

    protected function runMigrate(?string $connection, array $path, bool $realPath): void
    {
        $this->runCommand(Names::MIGRATE, [
            '--' . Options::CONNECTION => $connection,
            '--' . Options::PATH       => $path,
            '--' . Options::REALPATH   => $realPath,
            '--' . Options::FORCE      => true,
        ]);
    }
}
