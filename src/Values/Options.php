<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Values;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class Options extends DataTransferObject
{
    public bool $before = false;

    public ?string $connection = null;

    public bool $force = false;

    public ?string $name = null;

    public ?string $path = null;

    public bool $realpath = false;

    public ?int $step = null;
}
