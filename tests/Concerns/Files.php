<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\File;

trait Files
{
    protected function freshFiles(): void
    {
        File::deleteDirectory(
            database_path('actions')
        );
    }

    protected function copyFiles(): void
    {
        File::copyDirectory(
            __DIR__ . '/../fixtures/actions',
            database_path('actions')
        );
    }
}
