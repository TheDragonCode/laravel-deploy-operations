<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Concerns;

use Illuminate\Container\Container;
use Illuminate\Support\Composer as IlluminateComposer;

trait Composer
{
    protected ?IlluminateComposer $composer = null;

    protected function composer(): IlluminateComposer
    {
        if (! is_null($this->composer)) {
            return $this->composer;
        }

        return $this->composer = Container::getInstance()->make(IlluminateComposer::class);
    }
}
