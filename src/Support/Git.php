<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Support;

use Illuminate\Support\Str;

class Git
{
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

    protected function resolvePath(string $path): ?string
    {
        return realpath($path) ?: null;
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
            return Str::endsWith($path, '.git');
        }

        return false;
    }
}
