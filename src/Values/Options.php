<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Values;

use DragonCode\SimpleDataTransferObject\DataTransferObject;
use DragonCode\Support\Facades\Helpers\Str;

class Options extends DataTransferObject
{
    public bool $before = false;

    public ?string $connection = null;

    public bool $force = false;

    public ?string $name = null;

    public array $path = [];

    public bool $realpath = false;

    public ?int $step = null;

    protected function castName(?string $value): ?string
    {
        if (! empty($value)) {
            return Str::snake($value);
        }

        return null;
    }
}
