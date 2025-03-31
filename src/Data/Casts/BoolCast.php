<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Casts;

use DragonCode\Support\Facades\Helpers\Boolean;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class BoolCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): bool
    {
        return Boolean::parse($value);
    }
}
