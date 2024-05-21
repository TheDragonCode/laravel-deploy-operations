<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Events\DeployOperationEnded;
use DragonCode\LaravelDeployOperations\Events\DeployOperationFailed;
use DragonCode\LaravelDeployOperations\Events\DeployOperationStarted;
use DragonCode\LaravelDeployOperations\Events\NoPendingDeployOperations;
use DragonCode\Support\Facades\Helpers\Str;
use Throwable;

class Operations extends Processor
{
    public function handle(): void
    {
        $this->showCaption();
        $this->ensureRepository();
        $this->runOperations($this->getCompleted());
    }

    protected function showCaption(): void
    {
        $this->notification->info('Running operations');
    }

    protected function ensureRepository(): void
    {
        $this->runCommand(Names::Install, [
            '--' . Options::Connection => $this->options->connection,
            '--' . Options::Force      => true,
            '--' . Options::Mute       => true,
        ]);
    }

    protected function runOperations(array $completed): void
    {
        try {
            if ($files = $this->getNewFiles($completed)) {
                $this->fireEvent(DeployOperationStarted::class, 'up');

                $this->runEach($files, $this->getBatch());

                $this->fireEvent(DeployOperationEnded::class, 'up');

                return;
            }

            $this->fireEvent(NoPendingDeployOperations::class, 'up');
        }
        catch (Throwable $e) {
            $this->fireEvent(DeployOperationFailed::class, 'up');

            throw $e;
        }
    }

    protected function runEach(array $files, int $batch): void
    {
        foreach ($files as $file) {
            $this->run($file, $batch);
        }
    }

    protected function run(string $filename, int $batch): void
    {
        $this->migrator->runUp($filename, $batch, $this->options);
    }

    protected function getNewFiles(array $completed): array
    {
        return $this->getFiles(
            path: $this->options->path,
            filter: fn (string $file) => ! Str::of($file)->replace('\\', '/')->contains($completed)
        );
    }

    protected function getCompleted(): array
    {
        return $this->repository->getCompleted()->pluck('operation')->all();
    }

    protected function getBatch(): int
    {
        return $this->repository->getNextBatchNumber();
    }
}
