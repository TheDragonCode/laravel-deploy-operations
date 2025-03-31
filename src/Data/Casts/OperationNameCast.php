<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Casts;

use DragonCode\Support\Facades\Helpers\Str;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class OperationNameCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?string
    {
        if (empty($value)) {
            return null;
        }

        return Str::of($value)
            ->replace('\\', '/')
            ->replace('.php', '')
            ->explode('/')
            ->map(fn (string $path) => Str::snake($path))
            ->implode(DIRECTORY_SEPARATOR)
            ->toString();
    }
}
