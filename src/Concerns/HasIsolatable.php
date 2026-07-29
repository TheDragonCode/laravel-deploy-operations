<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Concerns;

use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Services\MutexService;

use function app;
use function is_numeric;

trait HasIsolatable
{
    protected function isolationMutex(): MutexService
    {
        return app(MutexService::class);
    }

    protected function isolatedStatusCode(): int
    {
        $isolate = $this->getIsolateOption();

        return is_numeric($isolate) ? (int) $isolate : self::SUCCESS;
    }

    protected function getIsolateOption(): bool|int|string
    {
        return $this->hasIsolateOption() ? ($this->option(Options::Isolated) ?? true) : false;
    }

    protected function hasIsolateOption(): bool
    {
        return $this->hasOption(Options::Isolated) && $this->option(Options::Isolated) !== false;
    }
}
