<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Enums;

enum MethodEnum: string
{
    case Up   = 'up';
    case Down = 'down';
}
