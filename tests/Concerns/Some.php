<?php

declare(strict_types=1);

namespace Tests\Concerns;

class Some
{
    public function get(string $value): string
    {
        return $value;
    }
}
