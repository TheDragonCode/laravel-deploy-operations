<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Casts;

use DragonCode\LaravelDeployOperations\Helpers\ConfigHelper;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function app;

class PathCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): string
    {
        if ($properties['realpath'] ?? false) {
            return $value ?: $this->config()->basePath();
        }

        return $this->config()->basePath($value);
    }

    protected function config(): ConfigHelper
    {
        return app(ConfigHelper::class);
    }
}
