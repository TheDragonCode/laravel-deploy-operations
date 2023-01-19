<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Constants\Options;
use DragonCode\LaravelActions\Services\Mutex;

trait Isolatable
{
    protected function isolationCreate(): bool
    {
        return $this->isolationMutex()->create($this);
    }

    protected function isolationForget(): void
    {
        $this->isolationMutex()->forget($this);
    }

    protected function isolationMutex(): Mutex
    {
        return $this->getLaravel()->make(Mutex::class);
    }

    protected function isolatedStatusCode(): int
    {
        return (int) (is_numeric($this->option(Options::ISOLATED)) ? $this->option(Options::ISOLATED) : self::SUCCESS);
    }
}
