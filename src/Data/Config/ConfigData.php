<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Data\Config;

use DragonCode\LaravelDeployOperations\Data\Casts\Config\ExcludeCast;
use DragonCode\LaravelDeployOperations\Data\Casts\Config\PathCast;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class ConfigData extends Data
{
    public ?string $connection;

    public string $table;

    #[WithCast(PathCast::class)]
    public string $path;

    #[WithCast(ExcludeCast::class)]
    public ?array $exclude;

    public bool $async;

    public TransactionsData $transactions;

    public QueueData $queue;

    public ShowData $show;
}
