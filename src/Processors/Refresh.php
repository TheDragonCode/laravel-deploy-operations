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
        $path       = $this->options->path;

        $this->runReset($connection, $path);
        $this->runMigrate($connection, $path);
    }

    protected function runReset(?string $connection, ?string $path, bool $realPath = true): void
    {
        $this->runCommand(Names::RESET, [
            '--' . Options::CONNECTION => $connection,
            '--' . Options::PATH       => $path,
            '--' . Options::REALPATH   => $realPath,
            '--' . Options::FORCE      => true,
        ]);
    }

    protected function runMigrate(?string $connection, ?string $path, bool $realPath = true): void
    {
        $this->runCommand(Names::MIGRATE, [
            '--' . Options::CONNECTION => $connection,
            '--' . Options::PATH       => $path,
            '--' . Options::REALPATH   => $realPath,
        ]);
    }
}
