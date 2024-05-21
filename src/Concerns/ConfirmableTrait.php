<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Concerns;

use Illuminate\Console\ConfirmableTrait as BaseConfirmableTrait;

trait ConfirmableTrait
{
    use BaseConfirmableTrait;

    protected bool $secure = true;

    protected function allowToProceed(): bool
    {
        return ! $this->secure || $this->confirmToProceed();
    }
}
