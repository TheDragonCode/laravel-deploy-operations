<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Config;

use Spatie\LaravelData\Data;

class QueueData extends Data
{
    public ?string $connection;

    public ?string $name;
}
