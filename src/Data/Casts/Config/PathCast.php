<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Casts\Config;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function rtrim;

class PathCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): string
    {
        return rtrim($value, '\\/') . DIRECTORY_SEPARATOR;
    }
}
