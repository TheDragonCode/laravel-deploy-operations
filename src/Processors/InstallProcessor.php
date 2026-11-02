<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use Illuminate\Support\Str;

class InstallProcessor extends Processor
{
    public function handle(): void
    {
        if ($this->exists()) {
            $this->notification->info('Operations repository already exists');

            return;
        }

        $this->notification->task('Installing the operation repository', function () {
            $this->create();
            $this->ensureDirectory();
        });
    }

    protected function isFile(string $path): bool
    {
        return Str::of($path)->lower()->endsWith('.php');
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
            ? $this->file->ensureDirectoryExists($this->file->dirname($this->options->path), 0o755)
            : $this->file->ensureDirectoryExists($this->options->path, 0o755);
    }
}
