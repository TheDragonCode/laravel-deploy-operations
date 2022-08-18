<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Composer\InstalledVersions;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Foundation\Console\AboutCommand;

trait About
{
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
        return Str::of($this->getPackageValue('name'))
            ->after('/')
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    protected function getPackageVersion(): string
    {
        return $this->getPackageValue('pretty_version');
    }

    protected function getPackageValue(string $key): string
    {
        return Arr::get(InstalledVersions::getRootPackage(), $key);
    }
}
