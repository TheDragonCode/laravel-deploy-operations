<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Filesystem\Directory;

class Install extends Processor
{
    public function handle(): void
    {
        if ($this->exists()) {
            $this->notification->warning('Actions repository already exists');

            return;
        }

        $this->notification->task('Installing the action repository', function () {
            $this->create();
            $this->ensureDirectory();
        });
    }

    protected function exists(): bool
    {
        return $this->repository->repositoryExists();
    }

    protected function create(): void
    {
        $this->repository->createRepository();
    }

    protected function ensureDirectory(): void
    {
        Directory::ensureDirectory(
            $this->getActionsPath()
        );
    }
}
