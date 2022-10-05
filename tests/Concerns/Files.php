<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\File;

/** @mixin \Tests\TestCase */
trait Files
{
    protected function freshFiles(): void
    {
        File::deleteDirectory(
            $this->targetDirectory()
        );
    }

    protected function copyFiles(): void
    {
        File::copyDirectory(
            __DIR__ . '/../fixtures/app/actions',
            $this->targetDirectory()
        );
    }

    protected function copySuccessFailureMethod(): void
    {
        File::copy(
            __DIR__ . '/../fixtures/app/actions_failed/2021_12_23_165048_run_success_on_failed.php',
            $this->targetDirectory('2021_12_23_165048_run_success_on_failed.php')
        );
    }

    protected function copyFailedMethod(): void
    {
        File::copy(
            __DIR__ . '/../fixtures/app/actions_failed/2021_12_23_184029_run_failed_failure.php',
            $this->targetDirectory('2021_12_23_184029_run_failed_failure.php')
        );
    }

    protected function copySuccessTransaction(): void
    {
        File::copy(
            __DIR__ . '/../fixtures/app/stubs/2021_02_15_124237_test_success_transactions.stub',
            $this->targetDirectory('2021_02_15_124237_test_success_transactions.php')
        );
    }

    protected function copyFailedTransaction(): void
    {
        File::copy(
            __DIR__ . '/../fixtures/app/stubs/2021_02_15_124852_test_failed_transactions.stub',
            $this->targetDirectory('2021_02_15_124852_test_failed_transactions.php')
        );
    }

    protected function targetDirectory(?string $path = null): string
    {
        $dir = $this->getActionsPath();

        File::ensureDirectoryExists($dir);

        return rtrim($dir, '/\\') . '/' . ltrim($path, '/\\');
    }

    protected function getActionsPath(): string
    {
        return $this->app['config']->get('actions.path');
    }
}
