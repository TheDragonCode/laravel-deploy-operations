<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Config;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class QueueData extends Data
{
    public ?string $connection;

    public ?string $name;

    #[MapInputName('unique_for')]
    public ?int $uniqueFor = null;
}
