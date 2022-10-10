<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Services;

use DragonCode\LaravelActions\Action;
use DragonCode\LaravelActions\Contracts\Notification;
use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Values\Options;
use DragonCode\Support\Exceptions\FileNotFoundException;
use DragonCode\Support\Facades\Helpers\Str;
use DragonCode\Support\Filesystem\File;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Throwable;

class Migrator
{
    public function __construct(
        protected File $file,
        protected Notification $notification,
        protected ActionRepository $repository,
        protected Config $config,
        protected Application $laravel
    ) {
    }

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

    public function runUp(string $file, int $batch, Options $options): void
    {
        $action = $this->resolvePath($file);
        $name   = $this->resolveActionName($file);

        if ($this->allowAction($action, $name, $options)) {
            $this->hasAction($action, '__invoke')
                ? $this->runAction($action, $name, '__invoke')
                : $this->runAction($action, $name, 'up');

            if ($this->allowLogging($action)) {
                $this->log($name, $batch);
            }
        }
    }

    public function runDown(string $file): void
    {
        $action = $this->resolvePath($file);
        $name   = $this->resolveActionName($file);

        if ($this->hasAction($action, 'down')) {
            $this->runAction($action, $name, 'down');
        }

        $this->deleteLog($name);
    }

    protected function hasAction(Action $action, string $method): bool
    {
        return method_exists($action, $method);
    }

    protected function runAction(Action $action, string $name, string $method): void
    {
        $this->notification->task("Action: $name", function () use ($action, $method) {
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
        });
    }

    protected function runMethod(Action $action, string $method, bool $transactions, int $attempts): void
    {
        $callback = fn () => $this->laravel->call([$action, $method]);

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

    protected function allowAction(Action $action, string $name, Options $options): bool
    {
        if (! $this->allowEnvironment($action)) {
            $this->notification->info("Action: $name was skipped on this environment");

            return false;
        }

        if ($this->disallowBefore($action, $options)) {
            $this->notification->info("Action: $name was skipped by 'before' option");

            return false;
        }

        return true;
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

    protected function resolvePath(string $path): Action
    {
        if ($this->file->exists($path)) {
            return require $path;
        }

        throw new FileNotFoundException($path);
    }

    protected function resolveActionName(string $path): string
    {
        return Str::of(realpath($path))
            ->after(realpath($this->config->path()))
            ->ltrim('\\/')
            ->replace('\\', '/')
            ->toString();
    }
}
