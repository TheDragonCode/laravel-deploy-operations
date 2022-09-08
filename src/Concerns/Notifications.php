<?php

namespace DragonCode\LaravelActions\Concerns;

use DragonCode\LaravelActions\Notifications\Basic;
use DragonCode\LaravelActions\Notifications\Beautiful;
use DragonCode\LaravelActions\Notifications\Notification;
use Illuminate\Console\View\Components\Factory;

trait Notifications
{
    protected ?Notification $notification = null;

    protected function notification(): Notification
    {
        if (! is_null($this->notification)) {
            return $this->notification;
        }

        return $this->notification = class_exists(Factory::class)
            ? new Beautiful($this->output)
            : new Basic($this->output);
    }
}
