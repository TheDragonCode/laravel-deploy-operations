<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Events\ActionEnded;
use DragonCode\LaravelActions\Events\ActionStarted;
use DragonCode\LaravelActions\Events\NoPendingActions;

class Rollback extends Processor
{
    public function handle(): void
    {
        if ($this->tableNotFound() || $this->nothingToRollback()) {
            $this->fireEvent(NoPendingActions::class, 'down');

            return;
        }

        if ($actions = $this->getActions($this->options->step)) {
            $this->fireEvent(ActionStarted::class, 'down');

            $this->showCaption();
            $this->run($actions);

            $this->fireEvent(ActionEnded::class, 'down');

            return;
        }

        $this->fireEvent(NoPendingActions::class, 'down');
    }

    protected function showCaption(): void
    {
        $this->notification->info('Rollback Actions');
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
        $this->migrator->runDown($action, $this->options);
    }

    protected function nothingToRollback(): bool
    {
        if ($this->count() <= 0) {
            $this->notification->warning('Nothing To Rollback');

            return true;
        }

        return false;
    }

    protected function count(): int
    {
        return $this->repository->getLastBatchNumber();
    }
}
