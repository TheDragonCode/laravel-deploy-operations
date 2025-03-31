<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data;

use DragonCode\LaravelDeployOperations\Data\Casts\BoolCast;
use DragonCode\LaravelDeployOperations\Data\Casts\OperationNameCast;
use DragonCode\LaravelDeployOperations\Data\Casts\PathCast;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class OptionsData extends Data
{
    #[WithCast(BoolCast::class)]
    public bool $before = false;

    public ?string $connection = null;

    #[WithCast(BoolCast::class)]
    public bool $force = false;

    #[WithCast(OperationNameCast::class)]
    public ?string $name = null;

    #[WithCast(PathCast::class)]
    public ?string $path = null;

    #[WithCast(BoolCast::class)]
    public bool $realpath = false;

    public ?int $step = null;

    public bool $mute = false;

    public bool $sync = false;

    public static function prepareForPipeline(array $properties): array
    {
        $properties['path'] ??= '';

        return $properties;
    }
}
