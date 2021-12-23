<?php

namespace Tests\Commands;

use Exception;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

class RollbackTest extends TestCase
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

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 8);
        $this->assertDatabaseMigrationHas($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_many_environments');
        $this->artisan('migrate:actions')->run();

        $this->artisan('migrate:actions:rollback')->run();
        $this->assertDatabaseCount($table, 10);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_many_environments');
    }

    public function testDownSuccess()
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success');
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 8);
        $this->assertDatabaseMigrationHas($this->table, 'run_success');

        $this->artisan('migrate:actions:rollback')->run();
        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success');
    }

    public function testDownSuccessOnFailed()
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success_on_failed');
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 8);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success_on_failed');

        try {
            $this->copySuccessFailureMethod();

            $this->table()->insert(['migration' => '2021_12_23_165048_run_success_on_failed', 'batch' => 999]);

            $this->artisan('migrate:actions:rollback')->run();
        } catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_success_on_failed'));
        }

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 9);
        $this->assertDatabaseMigrationHas($this->table, 'run_success_on_failed');
    }

    public function testDownFailed()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed');
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 8);
        $this->assertDatabaseMigrationHas($this->table, 'run_failed');

        $this->artisan('migrate:actions:rollback')->run();
        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed');
    }

    public function testUpFailedOnException()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed_failure');
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 8);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed_failure');

        try {
            $this->copyFailedMethod();

            $this->table()->insert(['migration' => '2021_12_23_184029_run_failed_failure', 'batch' => 999]);

            $this->artisan('migrate:actions:rollback')->run();
        } catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_failed_failure'));
        }

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 9);
        $this->assertDatabaseMigrationHas($this->table, 'run_failed_failure');
    }
}
