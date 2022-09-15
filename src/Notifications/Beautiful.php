<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Notifications;

use Closure;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Container\Container;

class Beautiful extends Notification
{
    protected Factory|null $components;

    public function line(string $string, ?string $style = null): void
    {
        $this->components()->line($style, $string, $this->verbosity);
    }

    public function info(string $string): void
    {
        $this->components()->info($string, $this->verbosity);
    }

    public function warning(string $string): void
    {
        $this->components()->warn($string, $this->verbosity);
    }

    public function task(string $description, Closure $task): void
    {
        $this->components()->task($description, $task);
    }

    protected function components()
    {
        if (! is_null($this->components)) {
            return $this->components;
        }

        return $this->components = Container::getInstance()->make(Factory::class, ['output' => $this->output]);
    }
}
