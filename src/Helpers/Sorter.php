<?php

declare(strict_types=1);

namespace DragonCode\LaravelActions\Helpers;

use Closure;
use DragonCode\Support\Facades\Filesystem\Path;
use DragonCode\Support\Facades\Helpers\Arr;

class Sorter
{
    public function byValues(array $items): array
    {
        return Arr::sort($items, $this->callback());
    }

    public function byKeys(array $items): array
    {
        return Arr::ksort($items, $this->callback());
    }

    public function byRan(array $actions, array $completed): array
    {
        foreach ($actions as $value) {
            if (! in_array($value, $completed, true)) {
                $completed[] = $value;
            }
        }

        return $completed;
    }

    protected function callback(): Closure
    {
        return function (string $a, string $b): int {
            $current = Path::filename($a);
            $next    = Path::filename($b);

            if ($current === $next) {
                return 0;
            }

            return $current < $next ? -1 : 1;
        };
    }
}
