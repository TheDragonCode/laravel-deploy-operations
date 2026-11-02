<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function filter_var;

class BoolCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): bool
    {
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
