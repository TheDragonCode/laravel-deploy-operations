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
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_filter;
use function realpath;

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
        protected Filesystem $file,
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

        $files = $this->isFile($file) ? [$file] : $this->getFileNames($path, $filter);

        $files = Arr::where(
            $files,
            fn (string $path) => Str::endsWith($path, '.php') && ! Str::contains($path, $this->config->exclude)
        );

        return Arr::map($this->sorter->byValues($files), fn (string $value) => Str::before($value, '.php'));
    }

    protected function getFileNames(string $path, ?Closure $filter = null): array
    {
        $files = Finder::create()->files()->ignoreDotFiles(false)->ignoreVCS(false)->followLinks()->in($path);

        return (new Collection($files))
            ->map(fn (SplFileInfo $file) => $file->getRealPath())
            ->filter($filter)
            ->sort()
            ->map(fn (string $file) => Str::of($file)->after(realpath($path))->trim('\\/')->toString())
            ->values()
            ->all();
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
