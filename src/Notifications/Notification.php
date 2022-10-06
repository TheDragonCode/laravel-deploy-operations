<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Notifications;

use Closure;
use DragonCode\LaravelActions\Contracts\Notification as NotificationContract;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Notification implements NotificationContract
{
    protected ?OutputStyle $output = null;

    protected int $verbosity = OutputInterface::VERBOSITY_NORMAL;

    abstract public function line(string $string, ?string $style = null): void;

    abstract public function info(string $string): void;

    abstract public function warning(string $string): void;

    abstract public function task(string $description, Closure $task): void;

    abstract public function twoColumn(string $first, string $second): void;

    public function setOutput(OutputStyle $output): NotificationContract
    {
        $this->output = $output;

        return $this;
    }
}
