<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Notifications;

use Closure;
use Illuminate\Console\View\Components\Factory;

class Beautiful extends Notification
{
    protected Factory|null $components = null;

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

    public function twoColumn(string $first, string $second): void
    {
        $this->components()->twoColumnDetail($first, $second, $this->verbosity);
    }

    protected function components(): Factory
    {
        if (! is_null($this->components)) {
            return $this->components;
        }

        return $this->component = $this->canSpeak() ? new Factory($this->output) : optional();
    }
}
