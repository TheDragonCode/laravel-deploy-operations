<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Constants;

class Names
{
    public const Operations = 'operations';
    public const Fresh      = 'operations:fresh';
    public const Install    = 'operations:install';
    public const Make       = 'make:operation';
    public const Rollback   = 'operations:rollback';
    public const Status     = 'operations:status';
}
