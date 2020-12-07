<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class RollbackTest extends TestCase
{
    public function testRollbackCommand()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:install')->run();

        $this->assertTrue(
            Schema::hasTable($this->table)
        );

        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'RollbackOne'])->run();
        $this->artisan('make:migration:action', ['name' => 'RollbackTwo'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 2);

        $this->assertTrue(
            $this->table()->whereRaw('migration like \'%rollback_one\'')->exists()
        );

        $this->assertTrue(
            $this->table()->whereRaw('migration like \'%rollback_two\'')->exists()
        );

        $this->artisan('migrate:actions:rollback')->run();

        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 2);

        $this->artisan('make:migration:action', ['name' => 'RollbackTree'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 3);

        $this->assertTrue(
            $this->table()->whereRaw('migration like \'%rollback_tree\'')->exists()
        );
    }
}
