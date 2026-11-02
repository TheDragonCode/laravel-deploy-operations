<?php

declare(strict_types=1);

namespace DragonCode\LaravelDeployOperations\Helpers;

use Closure;

use function in_array;
use function is_array;
use function pathinfo;
use function uksort;
use function usort;

class SorterHelper
{
    public function byValues(array $items): array
    {
        usort($items, $this->callback());

        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $items[$key] = $this->byValues($value);
            }
        }

        return $items;
    }

    public function byKeys(array $items): array
    {
        uksort($items, $this->callback());

        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $items[$key] = $this->byKeys($value);
            }
        }

        return $items;
    }

    public function byRan(array $values, array $completed): array
    {
        foreach ($values as $value) {
            if (! in_array($value, $completed, true)) {
                $completed[] = $value;
            }
        }

        return $completed;
    }

    protected function callback(): Closure
    {
        return static function (string $a, string $b): int {
            $current = pathinfo($a, PATHINFO_FILENAME);
            $next    = pathinfo($b, PATHINFO_FILENAME);

            if ($current === $next) {
                return 0;
            }

            return $current < $next ? -1 : 1;
        };
    }
}
