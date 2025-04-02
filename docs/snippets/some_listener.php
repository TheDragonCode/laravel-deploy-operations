<?php

declare(strict_types=1);

namespace App\Listeners;

use DragonCode\LaravelDeployOperations\Events\BaseEvent;

class SomeOperationsListener
{
    public function handle(BaseEvent $event): void
    {
        $method   = $event->method; // MethodEnum object value
        $isBefore = $event->before; // boolean
    }
}
