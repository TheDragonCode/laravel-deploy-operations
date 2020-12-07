<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\File;

trait Files
{
    protected function freshFiles()
    {
        File::deleteDirectory(
            database_path('actions')
        );
    }
}
