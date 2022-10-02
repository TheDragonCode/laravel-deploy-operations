<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;

class Reset extends Processor
{
    public function handle(): void
    {
        $this->reset(
            $this->options->connection,
            $this->options->path,
            $this->options->realpath,
            $this->count()
        );
    }

    protected function reset(?string $connection, ?string $path, ?bool $realPath, ?int $step): void
    {
        $this->runCommand(Names::ROLLBACK, [
            '--' . Options::CONNECTION => $connection,
            '--' . Options::PATH       => $path,
            '--' . Options::REALPATH   => $realPath,
            '--' . Options::STEP       => $step,
            '--' . Options::FORCE      => true,
        ]);
    }

    protected function count(): int
    {
        return count($this->repository->getCompleted());
    }
}
