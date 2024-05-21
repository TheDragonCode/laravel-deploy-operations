<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;

class Refresh extends Processor
{
    public function handle(): void
    {
        $connection = $this->options->connection;
        $path       = $this->options->path;

        $this->runReset($connection, $path);
        $this->runOperations($connection, $path);
    }

    protected function runReset(?string $connection, ?string $path, bool $realPath = true): void
    {
        $this->runCommand(Names::Reset, [
            '--' . Options::Connection => $connection,
            '--' . Options::Path       => $path,
            '--' . Options::Realpath   => $realPath,
            '--' . Options::Force      => true,
        ]);
    }

    protected function runOperations(?string $connection, ?string $path, bool $realPath = true): void
    {
        $this->runCommand(Names::Operations, [
            '--' . Options::Connection => $connection,
            '--' . Options::Path       => $path,
            '--' . Options::Realpath   => $realPath,
        ]);
    }
}
