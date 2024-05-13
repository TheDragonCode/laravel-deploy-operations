<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Services;

use DragonCode\LaravelActions\Action;
use DragonCode\LaravelActions\Contracts\Notification;
use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\LaravelActions\Jobs\ActionJob;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Values\Options;
use DragonCode\Support\Exceptions\FileNotFoundException;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Filesystem\File;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Throwable;

use function in_array;
use function method_exists;
use function realpath;

class Migrator
{
    public function __construct(
        protected File $file,
        protected Notification $notification,
        protected ActionRepository $repository,
        protected Config $config,
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

    public function runUp(string $filename, int $batch, Options $options): void
    {
        $path   = $this->resolvePath($filename, $options->path);
        $action = $this->resolveAction($path);
        $name   = $this->resolveActionName($path);

        if (! $this->allowAction($action, $options)) {
            $this->notification->twoColumn($name, '<fg=yellow;options=bold>SKIPPED</>');

            return;
        }

        if ($this->hasAsync($action, $options)) {
            ActionJob::dispatch($name);

            return;
        }

        $this->notification->task($name, function () use ($action, $name, $batch) {
            $this->hasAction($action, '__invoke')
                ? $this->runAction($action, '__invoke')
                : $this->runAction($action, 'up');

            if ($this->allowLogging($action)) {
                $this->log($name, $batch);
            }
        });
    }

    public function runDown(string $filename, Options $options): void
    {
        $path   = $this->resolvePath($filename, $options->path);
        $action = $this->resolveAction($path);
        $name   = $this->resolveActionName($path);

        $this->notification->task($name, function () use ($action, $name) {
            $this->runAction($action, 'down');
            $this->deleteLog($name);
        });
    }

    protected function runAction(Action $action, string $method): void
    {
        if ($this->hasAction($action, $method)) {
            try {
                $this->runMethod($action, $method, $action->enabledTransactions(), $action->transactionAttempts());

                $action->success();
            }
            catch (Throwable $e) {
                $action->failed();

                throw $e;
            }
        }
    }

    protected function hasAction(Action $action, string $method): bool
    {
        return method_exists($action, $method);
    }

    protected function hasAsync(Action $action, Options $options): bool
    {
        return ! $options->sync && $action->isAsync();
    }

    protected function runMethod(Action $action, string $method, bool $transactions, int $attempts): void
    {
        $callback = fn () => $this->container->call([$action, $method]);

        $transactions ? DB::transaction($callback, $attempts) : $callback();
    }

    protected function log(string $name, int $batch): void
    {
        $this->repository->log($name, $batch);
    }

    protected function deleteLog(string $name): void
    {
        $this->repository->delete($name);
    }

    protected function allowAction(Action $action, Options $options): bool
    {
        if (! $this->allowEnvironment($action)) {
            return false;
        }

        return ! $this->disallowBefore($action, $options);
    }

    protected function allowEnvironment(Action $action): bool
    {
        $env = $this->config->environment();

        return $action->allow()
            && $this->onEnvironment($env, $action->onEnvironment())
            && $this->exceptEnvironment($env, $action->exceptEnvironment());
    }

    protected function onEnvironment(?string $env, array $on): bool
    {
        return empty($on) || in_array($env, $on);
    }

    protected function exceptEnvironment(?string $env, array $except): bool
    {
        return empty($except) || ! in_array($env, $except);
    }

    protected function disallowBefore(Action $action, Options $options): bool
    {
        return $options->before && ! $action->hasBefore();
    }

    protected function allowLogging(Action $action): bool
    {
        return $action->isOnce();
    }

    protected function resolvePath(string $filename, string $path): string
    {
        $withExtension = Str::finish($filename, '.php');

        if ($this->file->exists($withExtension) && $this->file->isFile($withExtension)) {
            return $withExtension;
        }

        return Str::finish($path . DIRECTORY_SEPARATOR . $filename, '.php');
    }

    protected function resolveAction(string $path): Action
    {
        if ($this->file->exists($path)) {
            return require $path;
        }

        throw new FileNotFoundException($path);
    }

    protected function resolveActionName(string $path): string
    {
        return Str::of(realpath($path))
            ->after(realpath($this->config->path()) . DIRECTORY_SEPARATOR)
            ->replace(['\\', '/'], '/')
            ->before('.php')
            ->toString();
    }
}
