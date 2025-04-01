<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Services;

use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;
use DragonCode\LaravelDeployOperations\Data\OptionsData;
use DragonCode\LaravelDeployOperations\Enums\StatusEnum;
use DragonCode\LaravelDeployOperations\Jobs\OperationJob;
use DragonCode\LaravelDeployOperations\Notifications\Notification;
use DragonCode\LaravelDeployOperations\Operation;
use DragonCode\LaravelDeployOperations\Repositories\OperationsRepository;
use DragonCode\Support\Exceptions\FileNotFoundException;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Filesystem\File;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Throwable;

use function file_exists;
use function is_file;
use function method_exists;
use function realpath;

class MigratorService
{
    public function __construct(
        protected File $file,
        protected Notification $notification,
        protected OperationsRepository $repository,
        protected ConfigData $config,
        protected Container $container
    ) {}

    public function setConnection(?string $connection): self
    {
        $this->repository->setConnection($connection);

        return $this;
    }

    public function setOutput(OutputStyle $output): self
    {
        $this->notification->setOutput($output);

        return $this;
    }

    public function runUp(string $filename, int $batch, OptionsData $options): void
    {
        $path      = $this->resolvePath($filename, $options->path);
        $operation = $this->resolveOperation($path);
        $name      = $this->resolveOperationName($path);

        if (! $this->allowOperation($operation, $options)) {
            $this->notification->twoColumn($name, StatusEnum::Skipped->toColor());

            return;
        }

        if ($this->hasAsync($operation, $options)) {
            OperationJob::dispatch($name);

            $this->notification->twoColumn($name, StatusEnum::Pending->toColor());

            return;
        }

        $this->notification->task($name, function () use ($operation, $name, $batch) {
            $this->hasOperation($operation, '__invoke')
                ? $this->runOperation($operation, '__invoke')
                : $this->runOperation($operation, 'up');

            if ($operation->shouldOnce()) {
                $this->log($name, $batch);
            }
        });
    }

    public function runDown(string $filename, OptionsData $options): void
    {
        $path      = $this->resolvePath($filename, $options->path);
        $operation = $this->resolveOperation($path);
        $name      = $this->resolveOperationName($path);

        $this->notification->task($name, function () use ($operation, $name) {
            $this->runOperation($operation, 'down');
            $this->deleteLog($name);
        });
    }

    protected function runOperation(Operation $operation, string $method): void
    {
        if ($this->hasOperation($operation, $method)) {
            try {
                $this->runMethod($operation, $method, $operation->withinTransactions());

                $operation->success();
            }
            catch (Throwable $e) {
                $operation->failed();

                throw $e;
            }
        }
    }

    protected function hasOperation(Operation $operation, string $method): bool
    {
        return method_exists($operation, $method);
    }

    protected function hasAsync(Operation $operation, OptionsData $options): bool
    {
        return ! $options->sync && $operation->shouldBeAsync();
    }

    protected function runMethod(Operation $operation, string $method, bool $transactions): void
    {
        $callback = fn () => $this->container->call([$operation, $method]);

        $transactions ? DB::transaction($callback, $this->config->transactions->attempts) : $callback();
    }

    protected function log(string $name, int $batch): void
    {
        $this->repository->log($name, $batch);
    }

    protected function deleteLog(string $name): void
    {
        $this->repository->delete($name);
    }

    protected function allowOperation(Operation $operation, OptionsData $options): bool
    {
        if (! $operation->shouldRun()) {
            return false;
        }

        return ! $this->disallowBefore($operation, $options);
    }

    protected function disallowBefore(Operation $operation, OptionsData $options): bool
    {
        return $options->before && ! $operation->needBefore();
    }

    protected function resolvePath(string $filename, string $path): string
    {
        if (file_exists($path) && is_file($path)) {
            return $path;
        }

        $withExtension = Str::finish($filename, '.php');

        if ($this->file->exists($withExtension) && $this->file->isFile($withExtension)) {
            return $withExtension;
        }

        return Str::finish($path . DIRECTORY_SEPARATOR . $filename, '.php');
    }

    protected function resolveOperation(string $path): Operation
    {
        if ($this->file->exists($path)) {
            return require $path;
        }

        throw new FileNotFoundException($path);
    }

    protected function resolveOperationName(string $path): string
    {
        return Str::of(realpath($path))
            ->after(realpath($this->config->path) . DIRECTORY_SEPARATOR)
            ->replace(['\\', '/'], '/')
            ->before('.php')
            ->toString();
    }
}
