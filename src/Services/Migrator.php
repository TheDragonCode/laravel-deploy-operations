<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Services;

use DragonCode\LaravelActions\Action;
use DragonCode\LaravelActions\Helpers\Config;
use DragonCode\LaravelActions\Notifications\Notification;
use DragonCode\LaravelActions\Repositories\ActionRepository;
use DragonCode\LaravelActions\Values\Options;
use DragonCode\Support\Exceptions\FileNotFoundException;
use DragonCode\Support\Filesystem\File;
use Illuminate\Support\Facades\DB;
use Throwable;

class Migrator
{
    public function __construct(
        protected File             $file,
        protected Notification     $notification,
        protected ActionRepository $repository,
        protected Config           $config
    ) {
    }

    public function setConnection(?string $connection): self
    {
        $this->repository->setConnection($connection);

        return $this;
    }

    public function runUp(string $file, int $batch, Options $options): void
    {
        $action = $this->resolve($file);

        if ($this->allowAction($action, $file, $options)) {
            $this->runAction($action, $file, 'up');

            if ($this->allowLogging($action)) {
                $this->log($file, $batch);
            }
        }
    }

    public function runDown(string $file): void
    {
        $action = $this->resolve($file);

        $this->runAction($action, $file, 'down');

        $this->deleteLog($file);
    }

    protected function runAction(Action $action, string $name, string $method): void
    {
        $this->notification->task("Action: $name", function () use ($action, $method) {
            if (method_exists($action, $method)) {
                try {
                    $action->enabledTransactions()
                        ? DB::transaction(fn () => $this->runMethod($action, $method), $action->transactionAttempts())
                        : $this->runMethod($action, $method);
                }
                catch (Throwable $e) {
                    $action->failed();

                    throw $e;
                }
            }
        });
    }

    protected function runMethod(Action $action, string $method): void
    {
        $action->{$method}();

        $action->success();
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

        if (! $this->allowBefore($action, $options)) {
            $this->notification->info("Action: $name was skipped by 'before' option");

            return false;
        }

        return true;
    }

    protected function allowEnvironment(Action $action): bool
    {
        $env = $this->config->environment();

        $on     = $action->onEnvironment();
        $except = $action->exceptEnvironment();
        $allow  = $action->allow();

        if (! $allow) {
            return false;
        }

        if (! empty($on) && ! in_array($env, $on)) {
            return false;
        }

        return ! (! empty($except) && in_array($env, $except));
    }

    protected function allowBefore(Action $action, Options $options): bool
    {
        return $options->before && ! $action->hasBefore();
    }

    protected function allowLogging(Action $action): bool
    {
        return $action->isOnce();
    }

    protected function resolve(string $path): Action
    {
        if ($this->file->exists($path)) {
            return require $path;
        }

        throw new FileNotFoundException($path);
    }
}
