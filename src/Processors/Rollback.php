<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Events\ActionsEnded;
use DragonCode\LaravelActions\Events\ActionsStarted;
use DragonCode\LaravelActions\Events\NoPendingActions;

class Rollback extends Processor
{
    public function handle(): void
    {
        if ($this->tableNotFound() || $this->nothingToRollback()) {
            $this->fireEvent(new NoPendingActions('down'));

            return;
        }

        if ($actions = $this->getActions($this->options->step)) {
            $this->fireEvent(new ActionsStarted('down', $this->options->before));

            $this->run($actions);

            $this->fireEvent(new ActionsEnded('down', $this->options->before));

            return;
        }

        $this->fireEvent(new NoPendingActions('down'));
    }

    protected function run(array $actions): void
    {
        foreach ($actions as $row) {
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
