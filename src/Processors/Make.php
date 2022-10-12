<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Str;

class Make extends Processor
{
    protected string $fallbackName = 'auto';

    protected string $stub = __DIR__ . '/../../resources/stubs/action.stub';

    public function handle(): void
    {
        $this->notification->task('Creating an action', fn () => $this->run());
    }

    protected function run(): void
    {
        $name = $this->getName();
        $path = $this->getActionsPath($name, realpath: $this->options->realpath);

        $this->create($path);
    }

    protected function create(string $path): void
    {
        File::copy($this->stub, $path);
    }

    protected function getName(): string
    {
        $branch   = $this->getBranchName();
        $filename = $this->getFilename($branch);

        return Path::dirname($branch) . DIRECTORY_SEPARATOR . $filename;
    }

    protected function getFilename(string $branch): string
    {
        return Str::of(Path::filename($branch))->prepend($this->getTime())->finish('.php')->toString();
    }

    protected function getBranchName(): string
    {
        return $this->options->name ?? $this->git->currentBranch() ?? $this->fallbackName;
    }

    protected function getTime(): string
    {
        return date('Y_m_d_His_');
    }
}
