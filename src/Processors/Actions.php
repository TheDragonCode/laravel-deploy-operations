<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use DragonCode\LaravelActions\Constants\Names;
use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Events\ActionEnded;
use DragonCode\LaravelActions\Events\ActionFailed;
use DragonCode\LaravelActions\Events\ActionStarted;
use DragonCode\LaravelActions\Events\NoPendingActions;
use DragonCode\Support\Facades\Helpers\Str;
use Throwable;

class Actions extends Processor
{
    public function handle(): void
    {
        $this->showCaption();
        $this->ensureRepository();
        $this->runActions($this->getCompleted());
    }

    protected function showCaption(): void
    {
        $this->notification->info('Running actions');
    }

    protected function ensureRepository(): void
    {
        $this->runCommand(Names::INSTALL, [
            '--' . Options::CONNECTION => $this->options->connection,
            '--' . Options::FORCE      => true,
            '--' . Options::MUTE       => true,
        ]);
    }

    protected function runActions(array $completed): void
    {
        try {
            if ($files = $this->getNewFiles($completed)) {
                $this->fireEvent(ActionStarted::class, 'up');

                $this->runEach($files, $this->getBatch());

                $this->fireEvent(ActionEnded::class, 'up');

                return;
            }

            $this->fireEvent(NoPendingActions::class, 'up');
        }
        catch (Throwable $e) {
            $this->fireEvent(ActionFailed::class, 'up');

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
        return $this->repository->getCompleted()->pluck('action')->toArray();
    }

    protected function getBatch(): int
    {
        return $this->repository->getNextBatchNumber();
    }
}
