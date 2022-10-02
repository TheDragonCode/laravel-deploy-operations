<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

class Migrate extends Processor
{
    public function handle(): void
    {
        $this->runEach($this->getNewFiles(), $this->getBatch());
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
        $completed = $this->repository->getCompleted();

        return $this->getFiles(fn (string $file) => ! in_array($file, $completed));
    }

    protected function getBatch(): int
    {
        return $this->repository->getNextBatchNumber();
    }
}
