<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Casts\Config;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class ExcludeCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): array
    {
        return (new Collection($value))
            ->map(static fn (string $path) => Str::replace(['\\', '/'], DIRECTORY_SEPARATOR, $path))
            ->filter()
            ->all();
    }
}
