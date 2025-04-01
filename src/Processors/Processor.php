<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Processors;

use Closure;
use DragonCode\LaravelDeployOperations\Concerns\HasArtisan;
use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;
use DragonCode\LaravelDeployOperations\Data\OptionsData;
use DragonCode\LaravelDeployOperations\Enums\MethodEnum;
use DragonCode\LaravelDeployOperations\Helpers\GitHelper;
use DragonCode\LaravelDeployOperations\Helpers\SorterHelper;
use DragonCode\LaravelDeployOperations\Notifications\Notification;
use DragonCode\LaravelDeployOperations\Repositories\OperationsRepository;
use DragonCode\LaravelDeployOperations\Services\MigratorService;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Filesystem\File;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\Console\Input\InputInterface;

use function array_filter;

abstract class Processor
{
    use HasArtisan;

    abstract public function handle(): void;

    public function __construct(
        protected OptionsData $options,
        protected InputInterface $input,
        protected OutputStyle $output,
        protected ConfigData $config,
        protected OperationsRepository $repository,
        protected GitHelper $git,
        protected File $file,
        protected MigratorService $migrator,
        protected Notification $notification,
        protected Dispatcher $events,
        protected SorterHelper $sorter
    ) {
        $this->notification->setOutput($this->output, $this->options->mute);
        $this->repository->setConnection($this->options->connection);
        $this->migrator->setConnection($this->options->connection)->setOutput($this->output);
    }

    protected function getFiles(string $path, ?Closure $filter = null): array
    {
        $file = Str::finish($path, '.php');

        $files = $this->isFile($file) ? [$file] : $this->file->names($path, $filter, true);

        $files = Arr::filter(
            $files,
            fn (string $path) => Str::endsWith($path, '.php') && ! Str::contains($path, $this->config->exclude)
        );

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
            $this->notification->warning('Deploy operations table not found');

            return true;
        }

        return false;
    }

    protected function fireEvent(string $event, MethodEnum $method): void
    {
        $this->events->dispatch(new $event($method, $this->options->before));
    }

    protected function isFile(string $path): bool
    {
        return $this->file->exists($path) && $this->file->isFile($path);
    }
}
