<?php

namespace Tests\Commands;

use Exception;
use Tests\TestCase;

final class MigrateTest extends TestCase
{
    public function testMigrationCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'TestMigration'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'test_migration');
    }

    public function testOnce()
    {
        $this->copyFiles();

        $table = 'every_time';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
    }

    public function testSuccessTransaction()
    {
        $this->copySuccessTransaction();

        $table = 'transactions';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, $table);
    }

    public function testFailedTransaction()
    {
        $this->copyFailedTransaction();

        $table = 'transactions';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);

        try {
            $this->artisan('migrate:actions')->run();
        } catch (Exception $e) {
            $this->assertSame(Exception::class, get_class($e));
            $this->assertSame('Random message', $e->getMessage());
        }

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
    }

    public function testMigrationNotFound()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseHasTable($this->table);

        $this->artisan('migrate:actions:status')
            ->expectsOutput('No actions found');
    }
}
