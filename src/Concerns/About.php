<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Composer\InstalledVersions;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Foundation\Console\AboutCommand;

trait About
{
    protected string $package_name = 'dragon-code/laravel-migration-actions';

    protected function registerAbout(): void
    {
        if (class_exists(AboutCommand::class)) {
            AboutCommand::add($this->getPackageName(), fn () => [
                'Version' => $this->getPackageVersion(),
            ]);
        }
    }

    protected function getPackageName(): string
    {
        return Str::of($this->package_name)
            ->after('/')
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    protected function getPackageVersion(): string
    {
        return InstalledVersions::getPrettyVersion($this->package_name);
    }
}
