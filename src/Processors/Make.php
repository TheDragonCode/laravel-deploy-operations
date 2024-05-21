<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\Support\Facades\Filesystem\File;
use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Str;

use function base_path;
use function date;
use function realpath;

class Make extends Processor
{
    protected string $fallback = 'auto';

    protected string $defaultStub = __DIR__ . '/../../resources/stubs/deploy-operation.stub';

    public function handle(): void
    {
        $this->notification->task('Creating an operation', fn () => $this->run());
    }

    protected function run(): void
    {
        $name = $this->getName();
        $path = $this->getPath();

        $this->create($path . '/' . $name);
    }

    protected function create(string $path): void
    {
        File::copy($this->stubPath(), $path);
    }

    protected function getName(): string
    {
        $branch = $this->getBranchName();

        return $this->getFilename($branch);
    }

    protected function getPath(): string
    {
        return $this->options->path;
    }

    protected function getFilename(string $branch): string
    {
        $directory = Path::dirname($branch);
        $filename = Path::filename($branch);

        return Str::of($filename)->prepend($this->getTime())->finish('.php')->prepend($directory . '/')->toString();
    }

    protected function getBranchName(): string
    {
        return $this->options->name ?? $this->git->currentBranch() ?? $this->fallback;
    }

    protected function getTime(): string
    {
        return date('Y_m_d_His_');
    }

    protected function stubPath(): string
    {
        if ($path = realpath(base_path('stubs/deploy-operation.stub'))) {
            return $path;
        }

        return $this->defaultStub;
    }
}
