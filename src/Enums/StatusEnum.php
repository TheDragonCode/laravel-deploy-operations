<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Enums;

enum StatusEnum
{
    case Ran;
    case Pending;
    case Skipped;

    public function toColor(): string
    {
        return match ($this) {
            self::Ran     => '<fg=green;options=bold>Ran</>',
            self::Pending => '<fg=blue;options=bold>Pending</>',
            self::Skipped => '<fg=yellow;options=bold>Skipped</>',
        };
    }
}
