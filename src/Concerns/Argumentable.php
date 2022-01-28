<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Facades\Git;

/** @mixin \Illuminate\Console\Command */
trait Argumentable
{
    protected function argumentName(): string
    {
        if ($name = (string) $this->argument('name')) {
            return trim($name);
        }

        return $this->getNamePrefix() . '_' . time();
    }

    protected function getNamePrefix(): string
    {
        return $this->getGitBranchName() ?: 'auto';
    }

    protected function getGitBranchName(): ?string
    {
        return Git::currentBranch(base_path('.git'));
    }
}
