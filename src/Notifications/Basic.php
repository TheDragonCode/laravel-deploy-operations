<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Notifications;

use Closure;

class Basic extends Notification
{
    public function line(string $string, ?string $style = null): void
    {
        if ($this->canSpeak()) {
            $styled = $style ? "<$style>$string</$style>" : $string;

            $this->output->writeln($styled, $this->verbosity);
        }
    }

    public function info(string $string): void
    {
        $this->line($string, 'info');
    }

    public function warning(string $string): void
    {
        $this->line($string, 'warn');
    }

    public function task(string $description, Closure $task): void
    {
        $this->info($description);

        $start = microtime(true);

        $task();

        $run_time = number_format((microtime(true) - $start) * 1000, 2);

        $this->info("Done in {$run_time}ms");
    }

    public function twoColumn(string $first, string $second): void
    {
        $divider = str_pad('', 20, '.');

        $this->info($first . ' ' . $divider . ' ' . $second);
    }
}
