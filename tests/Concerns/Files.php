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

    protected function copyFiles(): void
    {
        $source = $this->isLatestApp()
            ? __DIR__ . '/../fixtures/app/9.x/actions'
            : __DIR__ . '/../fixtures/app/prev/actions';

        File::copyDirectory($source, $this->targetDirectory());
    }

    protected function copySuccessFailureMethod()
    {
        $source = $this->isLatestApp()
            ? __DIR__ . '/../fixtures/app/9.x/actions_failed/2021_12_23_165048_run_success_on_failed.php'
            : __DIR__ . '/../fixtures/app/prev/actions_failed/2021_12_23_165048_run_success_on_failed.php';

        File::copy($source, $this->targetDirectory('2021_12_23_165048_run_success_on_failed.php'));
    }

    protected function copyFailedMethod()
    {
        $source = $this->isLatestApp()
            ? __DIR__ . '/../fixtures/app/9.x/actions_failed/2021_12_23_184029_run_failed_failure.php'
            : __DIR__ . '/../fixtures/app/prev/actions_failed/2021_12_23_184029_run_failed_failure.php';

        File::copy($source, $this->targetDirectory('2021_12_23_184029_run_failed_failure.php'));
    }

    protected function copySuccessTransaction(): void
    {
        $source = $this->isLatestApp()
            ? __DIR__ . '/../fixtures/app/9.x/stubs/2021_02_15_124237_test_success_transactions.stub'
            : __DIR__ . '/../fixtures/app/prev/stubs/2021_02_15_124237_test_success_transactions.stub';

        File::copy($source, $this->targetDirectory('2021_02_15_124237_test_success_transactions.php'));
    }

    protected function copyFailedTransaction(): void
    {
        $source = $this->isLatestApp()
            ? __DIR__ . '/../fixtures/app/9.x/stubs/2021_02_15_124852_test_failed_transactions.stub'
            : __DIR__ . '/../fixtures/app/prev/stubs/2021_02_15_124852_test_failed_transactions.stub';

        File::copy($source, $this->targetDirectory('2021_02_15_124852_test_failed_transactions.php'));
    }

    protected function targetDirectory(string $path = null): string
    {
        $dir = database_path('actions');

        File::ensureDirectoryExists($dir);

        return rtrim($dir, '/\\') . '/' . ltrim($path, '/\\');
    }
}
