<?php

namespace DragonCode\LaravelActions;

use DragonCode\LaravelActions\Concerns\About;
use DragonCode\LaravelActions\Console;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    use About;

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerAbout();
        }
    }

    protected function registerCommands(): void
    {
        $this->commands([
            Console\Fresh::class,
            Console\Install::class,
            Console\Make::class,
            Console\Migrate::class,
            Console\Refresh::class,
            Console\Reset::class,
            Console\Rollback::class,
            Console\Status::class,
        ]);
    }
}
