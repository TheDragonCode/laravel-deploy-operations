<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Helpers\Str;

class Creator extends Processor
{
    public function handle()
    {
        $this->ensureExists();
    }

    protected function create(string $name, string $path): void
    {
        $this->files->copy(
            $this->getStub('action.stub'),
            $this->getTargetFilename($name, $path)
        );
    }

    protected function ensureExists(): void
    {
        $this->files->ensureDirectoryExists($this->getStub());
    }

    protected function getStub(?string $name = null): string
    {
        return __DIR__ . '/../../resources/stubs/' . $name;
    }

    protected function getTargetFilename(string $name, string $path): string
    {
        return Str::of($name)
            ->trim()
            ->slug('_')
            ->prepend(rtrim($path, '\\/') . '/')
            ->toString();
    }
}
