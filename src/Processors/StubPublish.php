<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\Support\Facades\Filesystem\File;

use function base_path;

class StubPublish extends Processor
{
    protected string $stub = __DIR__ . '/../../resources/stubs/deploy-operation.stub';

    public function handle(): void
    {
        $this->allow()
            ? $this->notification->task('Publishing', fn () => $this->publish())
            : $this->notification->info('Nothing to publish');
    }

    protected function publish(): void
    {
        File::copy($this->stub, $this->path());
    }

    protected function allow(): bool
    {
        return $this->options->force || $this->doesntExist();
    }

    protected function doesntExist(): bool
    {
        return !File::exists($this->path());
    }

    protected function path(): string
    {
        return base_path('stubs/deploy-operation.stub');
    }
}
