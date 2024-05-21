<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\LaravelDeployOperations\Events\DeployOperationEnded;
use DragonCode\LaravelDeployOperations\Events\DeployOperationStarted;
use DragonCode\LaravelDeployOperations\Events\NoPendingDeployOperations;

class Rollback extends Processor
{
    public function handle(): void
    {
        if ($this->tableNotFound() || $this->nothingToRollback()) {
            $this->fireEvent(NoPendingDeployOperations::class, 'down');

            return;
        }

        if ($items = $this->getOperations($this->options->step)) {
            $this->fireEvent(DeployOperationStarted::class, 'down');

            $this->showCaption();
            $this->run($items);

            $this->fireEvent(DeployOperationEnded::class, 'down');

            return;
        }

        $this->fireEvent(NoPendingDeployOperations::class, 'down');
    }

    protected function showCaption(): void
    {
        $this->notification->info('Rollback Operations');
    }

    protected function run(array $rows): void
    {
        foreach ($rows as $row) {
            $this->rollback($row->operation);
        }
    }

    protected function getOperations(?int $step): array
    {
        return (int)$step > 0
            ? $this->repository->getByStep($step)
            : $this->repository->getLast();
    }

    protected function rollback(string $item): void
    {
        $this->migrator->runDown($item, $this->options);
    }

    protected function nothingToRollback(): bool
    {
        if ($this->count() <= 0) {
            $this->notification->info('Nothing To Rollback');

            return true;
        }

        return false;
    }

    protected function count(): int
    {
        return $this->repository->getLastBatchNumber();
    }
}
