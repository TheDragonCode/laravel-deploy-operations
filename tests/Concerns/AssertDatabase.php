<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;

/** @mixin \Tests\TestCase */
trait AssertDatabase
{
    protected function assertDatabaseCount($table, int $count, $connection = null)
    {
        $actual = DB::connection($connection)->table($table)->count();

        $this->assertEquals($count, $actual);
    }
}
