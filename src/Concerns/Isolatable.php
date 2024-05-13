<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Services\Mutex;

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
        return $this->hasIsolateOption() ? (int) $this->option(Options::ISOLATED) : false;
    }

    protected function hasIsolateOption(): bool
    {
        return $this->hasOption(Options::ISOLATED) && $this->option(Options::ISOLATED);
    }
}
