<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Helpers\Arr;

class Status extends Processor
{
    public function handle(): void
    {
        if ($this->tableNotFound()) {
            return;
        }

        $this->run();
    }

    protected function run(): void
    {
        $completed = $this->getCompleted();
        $actions   = $this->getActionFiles();

        $this->showCaption();
        $this->showData($actions, $completed);
    }

    protected function showCaption(): void
    {
        $this->notification->twoColumn('<fg=gray>Action name</>', '<fg=gray>Batch / Status</>');
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
            $status = '<fg=green;options=bold>Ran</>';

            return "[$batch] $status";
        }

        return '<fg=yellow;options=bold>Pending</>';
    }

    protected function getCompleted(): array
    {
        return collect($this->repository->getCompleted())
            ->pluck('batch', 'action')
            ->toArray();
    }

    protected function getActionFiles(): array
    {
        return $this->getFiles(fn () => true);
    }
}
