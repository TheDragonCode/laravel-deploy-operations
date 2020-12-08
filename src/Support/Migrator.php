<?php

namespace Helldar\LaravelActions\Support;

use Illuminate\Database\Migrations\Migrator as BaseMigrator;

final class Migrator extends BaseMigrator
{
    public function usingConnection($name, callable $callback)
    {
        $prev = $this->resolver->getDefaultConnection();

        $this->setConnection($name);

        return tap($callback(), function () use ($prev) {
            $this->setConnection($prev);
        });
    }
}
