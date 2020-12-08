<?php

namespace Tests\Concerns;

trait Sleepable
{
    protected function await(): void
    {
        usleep(100);
    }
}
