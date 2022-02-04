<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Facades\Git;

/** @mixin \Illuminate\Console\Command */
trait Argumentable
{
    use Anonymous;

    protected $auto_prefix = 'auto';

    protected $branch_prefix = 'branch';

    protected function argumentName(): string
    {
        if ($name = (string) $this->argument('name')) {
            return trim($name);
        }

        return $this->makeName();
    }

    protected function makeName(): string
    {
        if ($this->allowAnonymous()) {
            return $this->getAutoPrefix();
        }

        return $this->getAutoPrefix() . '_' . time();
    }

    protected function getAutoPrefix(): string
    {
        return $this->getGitBranchName() ?: $this->auto_prefix;
    }

    protected function getGitBranchName(): ?string
    {
        $name = Git::currentBranch(base_path('.git'));

        preg_match('/^\d.*$/', $name, $output);

        if (! empty($output) && $this->disallowAnonymous()) {
            return $this->branch_prefix . '_' . $name;
        }

        return $name;
    }
}
