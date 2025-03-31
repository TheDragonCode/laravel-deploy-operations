<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\LaravelDeployOperations\Enums\StatusEnum;
use DragonCode\Support\Facades\Helpers\Arr;

use function sprintf;

class StatusProcessor extends Processor
{
    protected string $columnName = '<fg=gray>Operation name</>';

    protected string $columnStatus = '<fg=gray>Batch / Status</>';

    public function handle(): void
    {
        if ($this->tableNotFound()) {
            return;
        }

        [$files, $completed] = $this->getData();

        if ($this->isEmpty($files, $completed)) {
            $this->notification->info('No operations found');

            return;
        }

        $this->showCaption();
        $this->showHeaders();
        $this->showStatus($files, $completed);
    }

    protected function showCaption(): void
    {
        $this->notification->info('Show Status');
    }

    protected function showHeaders(): void
    {
        $this->notification->twoColumn($this->columnName, $this->columnStatus);
    }

    protected function showStatus(array $items, array $completed): void
    {
        foreach ($this->merge($items, array_keys($completed)) as $item) {
            $status = $this->getStatusFor($completed, $item);

            $this->notification->twoColumn($item, $status);
        }
    }

    protected function merge(array $items, array $completed): array
    {
        return $this->sorter->byRan($items, $completed);
    }

    protected function getData(): array
    {
        $files     = $this->getFiles($this->options->path);
        $completed = $this->getCompleted();

        return [$files, $completed];
    }

    protected function getStatusFor(array $completed, string $item): string
    {
        if ($batch = Arr::get($completed, $item)) {
            return sprintf('[%s] %s', $batch, StatusEnum::Ran->toColor());
        }

        return StatusEnum::Pending->toColor();
    }

    protected function getCompleted(): array
    {
        return $this->repository->getCompleted()
            ->pluck('batch', 'operation')
            ->all();
    }

    protected function isEmpty(array $items, array $completed): bool
    {
        return empty($items) && empty($completed);
    }
}
