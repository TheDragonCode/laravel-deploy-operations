<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Events;

class NoPendingActions extends BaseEvent
{
    public function __construct(
        public string $method
    ) {
    }
}
