<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Processors;

class Upgrade extends Processor
{
    public function handle(): void
    {
        $this->notification->warning('When upgrading to 5 from version 4, the project files do not change.');
    }
}
