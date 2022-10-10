<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Exception;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

class RollbackTest extends TestCase
{
    public function testRollbackCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'RollbackOne'])->assertSuccessful();
        $this->artisan(Names::MAKE, ['name' => 'RollbackTwo'])->assertSuccessful();

        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 2);

        $this->assertDatabaseMigrationHas($this->table, 'rollback_one');
        $this->assertDatabaseMigrationHas($this->table, 'rollback_two');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'rollback_tree');

        $this->artisan(Names::ROLLBACK)->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 2);

        $this->artisan(Names::MAKE, ['name' => 'RollbackTree'])->assertSuccessful();
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 3);

        $this->assertDatabaseMigrationHas($this->table, 'rollback_one');
        $this->assertDatabaseMigrationHas($this->table, 'rollback_two');
        $this->assertDatabaseMigrationHas($this->table, 'rollback_tree');
    }

    public function testEnvironment()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_many_environments');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_many_environments');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->artisan(Names::ROLLBACK)->assertSuccessful();
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

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_success');

        $this->artisan(Names::ROLLBACK)->assertSuccessful();
        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success');
    }

    public function testDownSuccessOnFailed()
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success_on_failed');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success_on_failed');

        try {
            $this->copySuccessFailureMethod();

            $this->table()->insert(['action' => '2021_12_23_165048_run_success_on_failed', 'batch' => 999]);

            $this->assertDatabaseCount($table, 2);
            $this->assertDatabaseCount($this->table, 12);
            $this->assertDatabaseMigrationHas($this->table, 'run_success_on_failed');

            $this->artisan(Names::ROLLBACK)->assertFailed();
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_success_on_failed'));
        }

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseMigrationHas($this->table, 'run_success_on_failed');
    }

    public function testDownFailed()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_failed');

        $this->artisan(Names::ROLLBACK)->assertSuccessful();
        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed');
    }

    public function testUpFailedOnException()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed_failure');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed_failure');

        try {
            $this->copyFailedMethod();

            $this->table()->insert(['action' => '2021_12_23_184029_run_failed_failure', 'batch' => 999]);

            $this->artisan(Names::ROLLBACK)->assertSuccessful();
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_failed_failure'));
        }

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseMigrationHas($this->table, 'run_failed_failure');
    }

    public function testDisabledBefore()
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationHas($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->artisan(Names::ROLLBACK)->assertSuccessful();

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
    }

    public function testEnabledBefore()
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE, ['--before' => true])->assertSuccessful();

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE, ['--before' => true])->assertSuccessful();

        $this->artisan(Names::ROLLBACK)->assertSuccessful();

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
    }
}
