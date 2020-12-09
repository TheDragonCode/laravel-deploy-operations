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

        $this->assertDatabaseHasLike($this->table, 'migration', 'rollback_one');
        $this->assertDatabaseHasLike($this->table, 'migration', 'rollback_two');
        $this->assertDatabaseDoesntLike($this->table, 'migration', 'rollback_tree');

        $this->artisan('migrate:actions:rollback')->run();

        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 2);

        $this->artisan('make:migration:action', ['name' => 'RollbackTree'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 3);

        $this->assertDatabaseHasLike($this->table, 'migration', 'rollback_one');
        $this->assertDatabaseHasLike($this->table, 'migration', 'rollback_two');
        $this->assertDatabaseHasLike($this->table, 'migration', 'rollback_tree');
    }
}
