<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Helpers;

use function base_path;
use function exec;
use function is_dir;
use function realpath;
use function rtrim;
use function sprintf;

class GitHelper
{
    public function currentBranch(?string $path = null): ?string
    {
        if ($this->hasGitDirectory($path)) {
            return $this->exec('rev-parse --abbrev-ref HEAD', $this->resolvePath($path));
        }

        return null;
    }

    protected function exec(string $command, ?string $path = null): ?string
    {
        return exec(sprintf('git -C "%s" %s', $path, $command));
    }

    protected function hasGitDirectory(?string $path = null): bool
    {
        if ($path = rtrim($this->resolvePath($path), '/\\')) {
            return is_dir($path . DIRECTORY_SEPARATOR . '.git');
        }

        return false;
    }

    protected function resolvePath(?string $path = null): string
    {
        return realpath($path ?: base_path());
    }
}
