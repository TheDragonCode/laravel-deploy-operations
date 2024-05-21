<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Concerns;

use DragonCode\LaravelDeployOperations\Constants\Options;
use DragonCode\LaravelDeployOperations\Services\Mutex;

use function is_numeric;

trait Isolatable
{
    protected function isolationMutex(): Mutex
    {
        return app(Mutex::class);
    }

    protected function isolatedStatusCode(): int
    {
        if ($isolate = $this->getIsolateOption()) {
            return is_numeric($isolate) ? $isolate : self::SUCCESS;
        }

        return self::SUCCESS;
    }

    protected function getIsolateOption(): bool|int
    {
        return $this->hasIsolateOption() ? (int) $this->option(Options::Isolated) : false;
    }

    protected function hasIsolateOption(): bool
    {
        return $this->hasOption(Options::Isolated) && $this->option(Options::Isolated);
    }
}
