<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Notifications;

use Closure;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
use Symfony\Component\Console\Output\OutputInterface;

class Notification
{
    protected ?OutputStyle $output = null;

    protected bool $silent = false;

    protected int $verbosity = OutputInterface::VERBOSITY_NORMAL;

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
        return $this->components ??= new Factory($this->output);
    }

    public function setOutput(OutputStyle $output, bool $silent = false): Notification
    {
        $this->output = $output;
        $this->silent = $silent;

        return $this;
    }

    protected function canSpeak(): bool
    {
        return ! $this->silent;
    }
}
