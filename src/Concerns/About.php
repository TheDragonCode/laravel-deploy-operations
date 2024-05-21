<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Concerns;

use Composer\InstalledVersions;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Foundation\Console\AboutCommand;

trait About
{
    protected string $composer = __DIR__ . '/../../composer.json';

    protected ?string $packageName = null;

    protected function registerAbout(): void
    {
        AboutCommand::add($this->getPackageName(), fn () => [
            'Version' => $this->getPackageVersion(),
        ]);
    }

    protected function getPackageName(): string
    {
        return Str::of($this->loadPackageName())
            ->after('/')
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    protected function getPackageVersion(): string
    {
        return InstalledVersions::getPrettyVersion($this->loadPackageName());
    }

    protected function loadPackageName(): string
    {
        return $this->packageName ??= Arr::ofFile($this->composer)->get('name');
    }
}
