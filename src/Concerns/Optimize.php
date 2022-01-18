<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Facades\Config;
use Illuminate\Support\Facades\Artisan as ArtisanSupport;

trait Optimize
{
    protected $optimize = false;

    public function hasOptimize(): bool
    {
        return $this->optimize;
    }

    public function optimize(): void
    {
        $this->optimizeConfig();
        $this->optimizeRoute();
        $this->optimizeView();
        $this->optimizeEvent();
    }

    protected function optimizeConfig(): void
    {
        if (Config::cache()->config) {
            ArtisanSupport::call('config:cache');
        }
    }

    protected function optimizeRoute(): void
    {
        if (Config::cache()->route) {
            ArtisanSupport::call('route:cache');
        }
    }

    protected function optimizeEvent(): void
    {
        if (Config::cache()->event) {
            ArtisanSupport::call('event:cache');
        }
    }

    protected function optimizeView(): void
    {
        if (Config::cache()->view) {
            ArtisanSupport::call('view:cache');
        }
    }
}
