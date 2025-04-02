<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\SomeOperationsListener;
use DragonCode\LaravelDeployOperations\Events\DeployOperationEnded;
use DragonCode\LaravelDeployOperations\Events\DeployOperationFailed;
use DragonCode\LaravelDeployOperations\Events\DeployOperationStarted;
use DragonCode\LaravelDeployOperations\Events\NoPendingDeployOperations;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DeployOperationStarted::class    => [SomeOperationsListener::class],
        DeployOperationEnded::class      => [SomeOperationsListener::class],
        DeployOperationFailed::class     => [SomeOperationsListener::class],
        NoPendingDeployOperations::class => [SomeOperationsListener::class],
    ];
}
