<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Reset extends Processor
{
    public function handle(): void
    {
        if ($this->tableNotFound() || $this->nothingToRollback()) {
            return;
        }

        $this->reset(
            $this->options->connection,
            $this->options->path,
            $this->options->realpath,
            $this->count()
        );
    }

    protected function reset(?string $connection, ?string $path, ?bool $realPath, ?int $step): void
    {
        $this->runCommand(Names::ROLLBACK, [
            '--' . Options::CONNECTION => $connection,
            '--' . Options::PATH       => $path,
            '--' . Options::REALPATH   => $realPath,
            '--' . Options::STEP       => $step,
            '--' . Options::FORCE      => true,
        ]);
    }

    protected function tableNotFound(): bool
    {
        if (! $this->repository->repositoryExists()) {
            $this->notification->warning('Actions table not found');

            return true;
        }

        return false;
    }

    protected function nothingToRollback(): bool
    {
        if ($this->count() <= 0) {
            $this->notification->warning('Nothing to rollback');

            return true;
        }

        return false;
    }

    protected function count(): int
    {
        return $this->repository->getLastBatchNumber();
    }
}
