<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Notifications;

use Symfony\Component\Console\Output\OutputInterface;

abstract class Notification
{
    protected int $verbosity = OutputInterface::VERBOSITY_NORMAL;

    public function __construct(
        protected OutputInterface $output
    ) {
    }

    abstract public function line(string $string, ?string $style = null): void;

    abstract public function info(string $string): void;

    abstract public function warning(string $string): void;
}
