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

    public function testEnvironment()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_many_environments');
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 5);
        $this->assertDatabaseMigrationHas($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_many_environments');
        $this->artisan('migrate:actions')->run();

        $this->artisan('migrate:actions:rollback')->run();
        $this->assertDatabaseCount($table, 8);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_many_environments');
    }
}
