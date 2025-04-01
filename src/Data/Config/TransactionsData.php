<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Config;

use Spatie\LaravelData\Data;

class TransactionsData extends Data
{
    public bool $enabled;

    public int $attempts;
}
