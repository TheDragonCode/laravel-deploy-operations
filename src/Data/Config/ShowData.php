<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Config;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class ShowData extends Data
{
    public bool $fullPath;
}
