<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Notifications;

use Closure;
use Illuminate\Console\View\Components\Factory;

class Beautiful extends Notification
{
    protected ?Factory $components = null;

    public function line(string $string, ?string $style = null): void
    {
        if ($this->canSpeak()) {
            $this->components()->line($style, $string, $this->verbosity);
        }
    }

    public function info(string $string): void
    {
        if ($this->canSpeak()) {
            $this->components()->info($string, $this->verbosity);
        }
    }

    public function warning(string $string): void
    {
        if ($this->canSpeak()) {
            $this->components()->warn($string, $this->verbosity);
        }
    }

    public function task(string $description, Closure $task): void
    {
        if ($this->canSpeak()) {
            $this->components()->task($description, $task);
            
            return;
        }
        
        $task();
    }

    public function twoColumn(string $first, string $second): void
    {
        if ($this->canSpeak()) {
            $this->components()->twoColumnDetail($first, $second, $this->verbosity);
        }
    }

    protected function components(): Factory
    {
        if (! is_null($this->components)) {
            return $this->components;
        }

        return $this->components = new Factory($this->output);
    }
}
