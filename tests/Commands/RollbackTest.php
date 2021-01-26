<?php

namespace Tests\Commands;

use Tests\TestCase;

final class RollbackTest extends TestCase
{
    public function testRollbackCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'RollbackOne'])->run();
        $this->artisan('make:migration:action', ['name' => 'RollbackTwo'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 2);

        $this->assertDatabaseMigrationHas($this->table, 'rollback_one');
        $this->assertDatabaseMigrationHas($this->table, 'rollback_two');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'rollback_tree');

        $this->artisan('migrate:actions:rollback')->run();

        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 2);

        $this->artisan('make:migration:action', ['name' => 'RollbackTree'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 3);

        $this->assertDatabaseMigrationHas($this->table, 'rollback_one');
        $this->assertDatabaseMigrationHas($this->table, 'rollback_two');
        $this->assertDatabaseMigrationHas($this->table, 'rollback_tree');
    }
}
