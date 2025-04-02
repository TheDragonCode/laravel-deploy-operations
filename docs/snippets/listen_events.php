<?php

declare(strict_types=1);

use App\Listeners\SomeOperationsListener;
use DragonCode\LaravelDeployOperations\Events\DeployOperationEnded;
use DragonCode\LaravelDeployOperations\Events\DeployOperationFailed;
use DragonCode\LaravelDeployOperations\Events\DeployOperationStarted;
use DragonCode\LaravelDeployOperations\Events\NoPendingDeployOperations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen([
            DeployOperationStarted::class,
            DeployOperationEnded::class,
            DeployOperationFailed::class,
            NoPendingDeployOperations::class,
        ], SomeOperationsListener::class);
    }
}
