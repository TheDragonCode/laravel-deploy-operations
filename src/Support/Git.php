<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Support;

use DragonCode\Support\Concerns\Makeable;
use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Filesystem\Path;
use Illuminate\Support\Str;

class Git
{
    use Makeable;

    public function currentBranch(?string $path): ?string
    {
        if ($path = $this->getGitPath($path)) {
            return $this->exec($path, 'rev-parse --abbrev-ref HEAD');
        }

        return null;
    }

    protected function exec(string $path, string $command): ?string
    {
        return exec(sprintf('git --git-dir %s %s', $path, $command));
    }

    protected function getGitPath(?string $path): ?string
    {
        if ($path = $this->resolvePath($path)) {
            if ($this->isGitDir($path)) {
                return $path;
            }
        }

        return null;
    }

    protected function isGitDir(?string $path): bool
    {
        if ($path = rtrim($path, '/\\')) {
            return Directory::exists($path . DIRECTORY_SEPARATOR . '.git');
        }

        return false;
    }

    protected function resolvePath(string $path): ?string
    {
        if ($path = realpath($path)) {
            $path = rtrim($path, '\\/');

            return Str::endsWith($path, '.git') ? Path::dirname($path) : $path;
        }

        return null;
    }
}
