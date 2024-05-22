<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Events;

use DragonCode\LaravelDeployOperations\Enums\MethodEnum;

abstract class BaseEvent
{
    public function __construct(
        public MethodEnum $method,
        public bool $before
    ) {}
}
