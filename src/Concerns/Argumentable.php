<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Support\Git;

trait Argumentable
{
    protected string $auto_prefix = 'auto';

    protected function argumentName(): string
    {
        if ($name = $this->argument('name')) {
            return trim((string) $name);
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
        return Git::make()->currentBranch(base_path('.git'));
    }
}
