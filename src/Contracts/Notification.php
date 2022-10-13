<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Contracts;

use Closure;
use Illuminate\Console\OutputStyle;

interface Notification
{
    public function info(string $string): void;

    public function line(string $string, ?string $style = null): void;

    public function setOutput(OutputStyle $output, bool $silent = false): self;

    public function task(string $description, Closure $task): void;

    public function twoColumn(string $first, string $second): void;

    public function warning(string $string): void;
}
