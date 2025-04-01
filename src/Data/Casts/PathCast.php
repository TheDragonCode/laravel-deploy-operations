<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Casts;

use DragonCode\LaravelDeployOperations\Helpers\ConfigHelper;
use Illuminate\Support\Str;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function app;
use function realpath;

class PathCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): string
    {
        $path = $this->config()->basePath((string) $value);

        if ($properties['realpath'] ?? false) {
            return $value ?: $path;
        }

        return $this->filename($path) ?: $path;
    }

    protected function filename(string $path): false|string
    {
        return realpath(Str::finish($path, '.php'));
    }

    protected function config(): ConfigHelper
    {
        return app(ConfigHelper::class);
    }
}
