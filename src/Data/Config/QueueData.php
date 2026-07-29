<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Config;

use DragonCode\LaravelDeployOperations\Jobs\OperationJob;
use Spatie\LaravelData\Data;

class QueueData extends Data
{
    public ?string $connection;

    public ?string $name;

    /** @var class-string<OperationJob> */
    public string $job = OperationJob::class;
}
