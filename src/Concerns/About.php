<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;

trait About
{
    protected function registerAbout(): void
    {
        if (class_exists(AboutCommand::class)) {
            AboutCommand::add('Migration Actions', fn () => [
                'Version' => $this->getPackageVersion(),
            ]);
        }
    }

    protected function getPackageVersion(): string
    {
        return InstalledVersions::getPrettyVersion('dragon-code/laravel-migration-actions');
    }
}
