<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations;

if (! function_exists('\DragonCode\LaravelDeployOperations\operation')) {
    function operation(string $filename): string
    {
        return $filename;
    }
}
