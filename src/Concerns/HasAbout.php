<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Concerns;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Str;

trait HasAbout
{
    protected string $packageName = 'dragon-code/laravel-deploy-operations';

    protected function registerAbout(): void
    {
        AboutCommand::add($this->getPackageName(), fn () => [
            'Version' => $this->getPackageVersion(),
        ]);
    }

    protected function getPackageName(): string
    {
        return Str::of($this->packageName)
            ->after('/')
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    protected function getPackageVersion(): string
    {
        return InstalledVersions::getPrettyVersion($this->packageName);
    }
}
