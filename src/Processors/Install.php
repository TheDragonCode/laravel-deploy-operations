<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

class Install extends Processor
{
    public function handle(): void
    {
        if ($this->exists()) {
            $this->notification->warning('Actions repository already exists.');

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

        $this->notification->info('Actions repository successfully created.');
    }
}
