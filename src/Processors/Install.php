<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Str;

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
        $this->isFile($this->options->path)
            ? Directory::ensureDirectory(Path::dirname($this->options->path))
            : Directory::ensureDirectory($this->options->path);
    }

    protected function isFile(string $path): bool
    {
        return Str::of($path)->lower()->endsWith('.php');
    }
}
