<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

class Rollback extends Processor
{
    public function handle(): void
    {
        if ($this->tableNotFound() || $this->nothingToRollback()) {
            return;
        }

        $this->run($this->options->step);
    }

    protected function run(?int $step): void
    {
        foreach ($this->getActions($step) as $row) {
            $this->rollbackAction($row->action);
        }
    }

    protected function getActions(?int $step): array
    {
        return (int) $step > 0
            ? $this->repository->getByStep($step)
            : $this->repository->getLast();
    }

    protected function rollbackAction(string $action): void
    {
        $this->migrator->runDown($action);
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
