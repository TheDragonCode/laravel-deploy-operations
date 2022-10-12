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

        [$files, $completed] = $this->getData();

        if ($this->doesntEmpty($files, $completed)) {
            $this->showCaption();
            $this->showStatus($files, $completed);

            return;
        }

        $this->notification->info('No actions found');
    }

    protected function showCaption(): void
    {
        $this->notification->twoColumn($this->columnName, $this->columnStatus);
    }

    protected function showStatus(array $actions, array $completed): void
    {
        foreach ($actions as $action) {
            $status = $this->getStatusFor($completed, $action);

            $this->notification->twoColumn($action, $status);
        }
    }

    protected function getData(): array
    {
        $files     = $this->getFiles(withExtension: false);
        $completed = $this->getCompleted();

        return [$files, $completed];
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

    protected function doesntEmpty(array $actions, array $completed): bool
    {
        return ! empty($actions) && ! empty($completed);
    }
}
