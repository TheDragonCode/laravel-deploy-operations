<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Illuminate\Foundation\Console\AboutCommand;

trait About
{
    protected function registerAbout(): void
    {
        if (class_exists(AboutCommand::class)) {
            AboutCommand::add('Laravel Migration Actions', fn () => [
                'Version' => '3.0.0',
            ]);
        }
    }
}
