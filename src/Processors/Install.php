<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

class Install extends Processor
{
    public function handle(): void
    {
        if ($this->exists()) {
            $this->notification->warning('Action repository already exists.');

            return;
        }

        $this->create();
    }

    protected function exists(): bool
    {
        return $this->repository->repositoryExists();
    }

    protected function create(): void
    {
        $this->repository->createRepository();

        $this->notification->info('Action repository successfully created.');
    }
}
