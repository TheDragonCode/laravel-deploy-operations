<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Facades\Config;
use Illuminate\Support\Facades\Artisan as ArtisanSupport;

trait Queueable
{
    protected $restart = false;

    public function hasRestart(): bool
    {
        return $this->restart;
    }

    public function restart(): void
    {
        $this->restartQueue();
        $this->restartHorizon();
        $this->restartOctane();
    }

    protected function restartQueue(): void
    {
        if (Config::queue()->queue) {
            ArtisanSupport::call('queue:restart');
        }
    }

    protected function restartHorizon(): void
    {
        if (Config::queue()->horizon) {
            ArtisanSupport::call('horizon:terminate');
        }
    }

    protected function restartOctane(): void
    {
        if (Config::queue()->octane) {
            ArtisanSupport::call('octane:reload');
        }
    }
}
