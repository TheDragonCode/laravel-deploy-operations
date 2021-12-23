<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\File;

trait Files
{
    protected function freshFiles(): void
    {
        File::deleteDirectory(
            $this->targetDirectory()
        );
    }

    protected function copyFiles(bool $failed = false): void
    {
        $source = $failed
            ? __DIR__ . '/../fixtures/actions_failed'
            : __DIR__ . '/../fixtures/actions';

        File::copyDirectory($source, $this->targetDirectory());
    }

    protected function copySuccessTransaction(): void
    {
        File::copy(
            __DIR__ . '/../fixtures/stubs/2021_02_15_124237_test_success_transactions.stub',
            $this->targetDirectory('2021_02_15_124237_test_success_transactions.php')
        );
    }

    protected function copyFailedTransaction(): void
    {
        File::copy(
            __DIR__ . '/../fixtures/stubs/2021_02_15_124852_test_failed_transactions.stub',
            $this->targetDirectory('2021_02_15_124852_test_failed_transactions.php')
        );
    }

    protected function targetDirectory(string $path = null): string
    {
        $dir = database_path('actions');

        File::ensureDirectoryExists($dir);

        return rtrim($dir, '/\\') . '/' . ltrim($path, '/\\');
    }
}
