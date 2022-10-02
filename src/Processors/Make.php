<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Str;

class Make extends Processor
{
    protected string $defaultName = 'auto';

    protected string $stub = __DIR__ . '/../../resources/stubs/action.stub';

    public function handle(): void
    {
        $path = $this->getPath();

        $this->create($path);
    }

    protected function create(string $path): void
    {
        File::copy($this->stub, $path);
    }

    protected function getPath(): string
    {
        $name = $this->getName();

        return $this->options->realpath ? $name : $this->config->path($name);
    }

    protected function getName(): string
    {
        $branch   = $this->getBranchName();
        $filename = $this->getFilename($branch);

        return Path::dirname($branch) . DIRECTORY_SEPARATOR . $filename;
    }

    protected function getFilename(string $branch): string
    {
        return Str::of(Path::filename($branch))->prepend($this->getTime())->end('.php')->toString();
    }

    protected function getBranchName(): string
    {
        return $this->options->name ?? $this->git->currentBranch() ?? $this->defaultName;
    }

    protected function getTime(): string
    {
        return date('Y_m_d_His_');
    }
}
