<?php

declare(strict_types=1);

namespace Illuminate\Contracts\Queue;

if (! interface_exists(ShouldBeUnique::class)) {
    interface ShouldBeUnique
    {
    }
}
