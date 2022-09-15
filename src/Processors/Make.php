<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Str;

class Make extends Processor
{
    protected string $default_name = 'auto';

    protected string $stub = __DIR__ . '/../../resources/stubs/action.stub';

    public function handle(): void
    {
        $path = $this->getPath();

        $this->ensureDirectory($path);
        $this->create($path);
    }

    protected function create(string $path): void
    {
        File::copy($this->stub, $path);
    }

    protected function ensureDirectory(string $path): void
    {
        Directory::ensureDirectory(
            Path::dirname($path)
        );
    }

    protected function getPath(): string
    {
        $name = $this->getName();

        return $this->options->realpath ? $name : $this->config->path($name);
    }

    protected function getName(): string
    {
        $name = $this->options->name ?? $this->git->currentBranch() ?? $this->default_name;

        $directory = Path::dirname($name);
        $filename  = Str::of(Path::filename($name))->prepend($this->getTime())->end('.php');

        return $directory . DIRECTORY_SEPARATOR . $filename;
    }

    protected function getTime(): string
    {
        return date('Y_m_d_His_');
    }
}
