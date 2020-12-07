<?php

namespace Tests\Concerns;

trait Settings
{
    protected $table = 'foo_actions';

    protected function setTable($app): void
    {
        $app['config']->set('actions.table', $this->table);
    }
}
