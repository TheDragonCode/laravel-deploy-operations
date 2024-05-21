<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Events;

abstract class BaseEvent
{
    public function __construct(
        public string $method,
        public bool $before
    ) {
    }
}
