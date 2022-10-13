<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use Closure;
use DragonCode\LaravelActions\Concerns\Artisan;
use DragonCode\LaravelActions\Contracts\Notification;
use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\LaravelActions\Helpers\Git;
use DragonCode\LaravelActions\Helpers\Sorter;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Services\Migrator;
use DragonCode\LaravelActions\Values\Options;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Filesystem\File;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\Console\Input\InputInterface;

abstract class Processor
{
    use Artisan;

    public function __construct(
        protected Options          $options,
        protected InputInterface   $input,
        protected OutputStyle      $output,
        protected Config           $config,
        protected ActionRepository $repository,
        protected Git              $git,
        protected File             $file,
        protected Migrator         $migrator,
        protected Notification     $notification,
        protected Dispatcher       $events,
        protected Sorter           $sorter
    ) {
        $this->notification->setOutput($this->output, $this->options->silent);
        $this->repository->setConnection($this->options->connection);
        $this->migrator->setConnection($this->options->connection)->setOutput($this->output);
    }

    abstract public function handle(): void;

    protected function getFiles(string $path, ?Closure $filter = null): array
    {
        $file = Str::finish($path, '.php');

        $files = $this->isFile($file) ? [$file] : $this->file->names($path, $filter, true);

        return Arr::of($this->sorter->byValues($files))
            ->map(fn (string $value) => Str::before($value, '.php'))
            ->toArray();
    }

    protected function runCommand(string $command, array $options = []): void
    {
        $this->artisan($command, array_filter($options), $this->output);
    }

    protected function tableNotFound(): bool
    {
        if (! $this->repository->repositoryExists()) {
            $this->notification->warning('Actions table not found');

            return true;
        }

        return false;
    }

    protected function fireEvent(string $event, string $method): void
    {
        $this->events->dispatch(new $event($method, $this->options->before));
    }

    protected function isFile(string $path): bool
    {
        return $this->file->exists($path) && $this->file->isFile($path);
    }
}
