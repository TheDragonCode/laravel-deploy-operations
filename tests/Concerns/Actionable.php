<?php

namespace Tests\Concerns;

trait Actionable
{
    protected function getMigrationPath(): string
    {
        return __DIR__ . '/../fixtures/actions';
    }
}
