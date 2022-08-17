<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Facades\Git;

/** @mixin \Illuminate\Console\Command */
trait Argumentable
{
    protected $auto_prefix = 'auto';

    protected function argumentName(): string
    {
        if ($name = (string) $this->argument('name')) {
            return trim($name);
        }

        return $this->makeName();
    }

    protected function makeName(): string
    {
        return $this->getAutoPrefix();
    }

    protected function getAutoPrefix(): string
    {
        return $this->getGitBranchName() ?: $this->auto_prefix;
    }

    protected function getGitBranchName(): ?string
    {
        return Git::currentBranch(base_path('.git'));
    }
}
