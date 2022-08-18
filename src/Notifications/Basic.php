<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Notifications;

class Basic extends Notification
{
    public function line(string $string, ?string $style = null): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->verbosity);
    }

    public function info(string $string): void
    {
        $this->line($string, 'info');
    }

    public function warning(string $string): void
    {
        $this->line($string, 'warn');
    }
}
