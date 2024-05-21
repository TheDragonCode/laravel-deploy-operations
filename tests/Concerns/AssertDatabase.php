<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** @mixin \Tests\TestCase */
trait AssertDatabase
{
    protected function assertDatabaseHasTable(string $table): void
    {
        $this->assertTrue(
            $this->hasTable($table)
        );
    }

    protected function assertDatabaseDoesntTable(string $table): void
    {
        $this->assertFalse(
            $this->hasTable($table)
        );
    }

    protected function assertDatabaseOperationHas(string $table, $value, $connection = null, string $column = 'operation'): void
    {
        $this->assertDatabaseHasLike($table, $column, $value, $connection);
    }

    protected function assertDatabaseHasLike(string $table, string $column, $value, $connection = null): void
    {
        $exists = DB::connection($connection)
            ->table($table)
            ->whereRaw("$column like '%$value%'")
            ->exists();

        $this->assertTrue($exists);
    }

    protected function assertDatabaseOperationDoesntLike(string $table, $value, $connection = null, string $column = 'operation'): void
    {
        $this->assertDatabaseDoesntLike($table, $column, $value, $connection);
    }

    protected function assertDatabaseDoesntLike(string $table, string $column, $value, $connection = null): void
    {
        $exists = DB::connection($connection)
            ->table($table)
            ->whereRaw("$column like '%$value%'")
            ->doesntExist();

        $this->assertTrue($exists);
    }

    protected function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }
}
