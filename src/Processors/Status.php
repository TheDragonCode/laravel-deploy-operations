<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Helpers\Arr;

class Status extends Processor
{
    protected string $columnName = '<fg=gray>Action name</>';

    protected string $columnStatus = '<fg=gray>Batch / Status</>';

    protected string $statusRan = '<fg=green;options=bold>Ran</>';

    protected string $statusPending = '<fg=yellow;options=bold>Pending</>';

    public function handle(): void
    {
        if ($this->tableNotFound()) {
            return;
        }

        $this->showCaption();
        $this->showStatus();
    }

    protected function showCaption(): void
    {
        $this->notification->twoColumn($this->columnName, $this->columnStatus);
    }

    protected function showStatus(): void
    {
        $this->showData(
            $this->getFiles(),
            $this->getCompleted()
        );
    }

    protected function showData(array $actions, array $completed): void
    {
        foreach ($actions as $action) {
            $status = $this->getStatusFor($completed, $action);

            $this->notification->twoColumn($action, $status);
        }
    }

    protected function getStatusFor(array $completed, string $action): string
    {
        if ($batch = Arr::get($completed, $action)) {
            return "[$batch] $this->statusRan";
        }

        return $this->statusPending;
    }

    protected function getCompleted(): array
    {
        return $this->repository->getCompleted()
            ->pluck('batch', 'action')
            ->toArray();
    }
}
