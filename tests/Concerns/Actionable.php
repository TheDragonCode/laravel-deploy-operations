<?php

namespace Tests\Concerns;

trait Actionable
{
    protected function getMigrationPath(): string
    {
        return $this->allowAnonymous()
            ? __DIR__ . '/../fixtures/app/anonymous/actions'
            : __DIR__ . '/../fixtures/app/named/actions';
    }
}
