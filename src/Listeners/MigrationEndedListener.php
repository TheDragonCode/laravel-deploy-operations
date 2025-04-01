<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Listeners;

use Illuminate\Database\Events\MigrationEnded;

class MigrationEndedListener extends Listener
{
    public function handle(MigrationEnded $event): void
    {
        if ($operation = $this->withOperation($event->migration)) {
            $this->run($event->method, $operation);
        }
    }
}
