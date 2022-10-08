<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Events\ActionsEnded;
use DragonCode\LaravelActions\Events\ActionsStarted;
use DragonCode\LaravelActions\Events\NoPendingActions;
use DragonCode\Support\Facades\Helpers\Str;

class Migrate extends Processor
{
    public function handle(): void
    {
        if ($files = $this->getNewFiles()) {
            $this->fireEvent(new ActionsStarted('up', $this->options->before));

            $this->runEach($files, $this->getBatch());

            $this->fireEvent(new ActionsEnded('up', $this->options->before));

            return;
        }

        $this->fireEvent(new NoPendingActions('up'));
    }

    protected function runEach(array $files, int $batch): void
    {
        foreach ($files as $file) {
            $this->run($file, $batch);
        }
    }

    protected function run(string $file, int $batch): void
    {
        $this->migrator->runUp($file, $batch, $this->options);
    }

    protected function getNewFiles(): array
    {
        $completed = $this->repository->getCompleted()->pluck('action')->toArray();

        return $this->getFiles(fn (string $file) => ! Str::of($file)->replace('\\', '/')->endsWith($completed));
    }

    protected function getBatch(): int
    {
        return $this->repository->getNextBatchNumber();
    }
}
