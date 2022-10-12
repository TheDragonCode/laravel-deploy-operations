<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

use Closure;
use DragonCode\LaravelActions\Concerns\Artisan;
use DragonCode\LaravelActions\Contracts\Notification;
use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\LaravelActions\Helpers\Git;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Services\Migrator;
use DragonCode\LaravelActions\Values\Options;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Filesystem\File;
use DragonCode\Support\Helpers\Ables\Arrayable;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\Console\Input\InputInterface;

abstract class Processor
{
    use Artisan;

    abstract public function handle(): void;

    public function __construct(
        protected Options $options,
        protected InputInterface $input,
        protected OutputStyle $output,
        protected Config $config,
        protected ActionRepository $repository,
        protected Git $git,
        protected File $file,
        protected Migrator $migrator,
        protected Notification $notification,
        protected Dispatcher $events
    ) {
        $this->notification->setOutput($this->output);
        $this->repository->setConnection($this->options->connection);
        $this->migrator->setConnection($this->options->connection)->setOutput($this->output);
    }

    protected function getFiles(?Closure $filter = null, ?string $path = null, bool $realpath = false, bool $fullpath = false, bool $withExtension = true): array
    {
        $path = $this->getActionsPath($path, $realpath);

        $names = $this->file->exists($path) ? [$path] : $this->file->allPaths($path, $filter, true);

        return Arr::of($names)
            ->when(
                ! $fullpath,
                fn (Arrayable $array) => $array
                    ->map(fn (string $value) => Str::of(realpath($value))->after(realpath($path))->ltrim('\\/')->toString())
            )
            ->when(
                ! $withExtension,
                fn (Arrayable $array) => $array
                    ->map(fn (string $value) => Str::before($value, '.php'))
            )
            ->toArray();
    }

    protected function getActionsPath(?string $path = null, bool $realpath = false): string
    {
        $path = $realpath ? $path : $this->config->path($path);

        if (! is_dir($path) && ! Str::endsWith($path, '.php')) {
            return $this->file->exists($path . '.php') ? $path . '.php' : $path;
        }

        return $path;
    }

    protected function runCommand(string $command, array $options = []): void
    {
        $this->artisan($command, array_filter($options));
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
}
