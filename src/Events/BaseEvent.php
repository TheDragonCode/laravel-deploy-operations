<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Events;

abstract class BaseEvent
{
    public function __construct(
        public string $method,
        public bool $before
    ) {
    }
}
