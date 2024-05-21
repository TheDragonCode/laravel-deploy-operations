<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;

class Reset extends Processor
{
    public function handle(): void
    {
        $this->rollback(
            $this->options->connection,
            $this->options->path,
            $this->count()
        );
    }

    protected function rollback(?string $connection, ?string $path, int $step): void
    {
        $this->runCommand(Names::Rollback, [
            '--' . Options::Connection => $connection,
            '--' . Options::Path       => $path,
            '--' . Options::Realpath   => true,
            '--' . Options::Step       => $step,
            '--' . Options::Force      => true,
        ]);
    }

    protected function count(): int
    {
        return $this->repository->getLastBatchNumber();
    }
}
