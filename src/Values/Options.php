<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Values;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class Options extends DataTransferObject
{
    public bool $before = false;

    public ?string $database = null;

    public bool $force = false;

    public ?string $path = null;

    public bool $realpath = false;

    public ?int $step = null;

    public ?string $name = null;
}
