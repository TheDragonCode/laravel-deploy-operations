<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Events;

class ActionsEnded extends BaseEvent
{
    public function __construct(
        public string $method,
        public bool   $before
    ) {
    }
}
