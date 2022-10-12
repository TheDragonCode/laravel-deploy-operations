<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Exception;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

class MigrateTest extends TestCase
{
    public function testMigrationCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'TestMigration'])->assertExitCode(0);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'test_migration');
    }

    public function testSameName()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'TestMigration'])->assertExitCode(0);

        sleep(2);

        $this->artisan(Names::MAKE, ['name' => 'TestMigration'])->assertExitCode(0);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 2);
        $this->assertDatabaseMigrationHas($this->table, 'test_migration');
    }

    public function testOnce()
    {
        $this->copyFiles();

        $table = 'every_time';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
    }

    public function testSuccessTransaction()
    {
        $this->copySuccessTransaction();

        $table = 'transactions';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, $table);
    }

    public function testFailedTransaction()
    {
        $this->copyFailedTransaction();

        $table = 'transactions';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);

        try {
            $this->artisan(Names::MIGRATE)->assertExitCode(0);
        }
        catch (Exception $e) {
            $this->assertSame(Exception::class, get_class($e));
            $this->assertSame('Random message', $e->getMessage());
        }

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, $table);
    }

    public function testSingleEnvironment()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_testing');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationHas($this->table, 'run_except_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_testing');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationHas($this->table, 'run_except_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_testing');
    }

    public function testManyEnvironments()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_many_environments');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_many_environments');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_many_environments');
        $this->assertDatabaseMigrationHas($this->table, 'run_except_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_many_environments');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_on_all');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_testing');
        $this->assertDatabaseMigrationHas($this->table, 'run_on_many_environments');
        $this->assertDatabaseMigrationHas($this->table, 'run_except_production');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_except_many_environments');
    }

    public function testAllow()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_allow');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_disallow');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_allow');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_disallow');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_allow');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_disallow');
    }

    public function testUpSuccess()
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_success');
    }

    public function testUpSuccessOnFailed()
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success_on_failed');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success_on_failed');

        try {
            $this->copySuccessFailureMethod();

            $this->artisan(Names::MIGRATE)->assertExitCode(0);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_success_on_failed'));
        }

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_success_on_failed');
    }

    public function testUpFailed()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'run_failed');
    }

    public function testUpFailedOnException()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed_failure');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed_failure');

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 11);

        try {
            $this->copyFailedMethod();

            $this->artisan(Names::MIGRATE)->assertExitCode(0);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_failed_failure'));
        }

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'run_failed_failure');
    }

    public function testPathAsFileWithExtension()
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path/2021_12_15_205804_baz.php';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'baz');
        $this->artisan(Names::MIGRATE, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'baz');
    }

    public function testPathAsFileWithoutExtension()
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path/2021_12_15_205804_baz';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'baz');
        $this->artisan(Names::MIGRATE, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'baz');
    }

    public function testPathAsDirectory()
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'baz');
        $this->artisan(Names::MIGRATE, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'baz');
    }

    public function testMigrationNotFound()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);

        $this->artisan(Names::STATUS)->assertExitCode(0);
    }

    public function testDisabledBefore()
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationHas($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationHas($this->table, 'test_before_disabled');
    }

    public function testEnabledBefore()
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE, ['--before' => true])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE, ['--before' => true])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
    }

    public function testMixedBefore()
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE, ['--before' => true])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE, ['--before' => true])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationHas($this->table, 'test_before_disabled');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseMigrationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseMigrationHas($this->table, 'test_before_disabled');
    }

    public function testDI(): void
    {
        $this->copyDI();

        $table = 'test';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'invoke');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'invoke_down');
        $this->assertDatabaseMigrationDoesntLike($this->table, 'up_down');
        $this->assertDatabaseMigrationDoesntLike($table, 'up_down', column: 'value');
        $this->assertDatabaseMigrationDoesntLike($table, 'invoke_down', column: 'value');
        $this->assertDatabaseMigrationDoesntLike($table, 'invoke', column: 'value');
        $this->artisan(Names::MIGRATE)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 3);
        $this->assertDatabaseMigrationHas($this->table, 'invoke');
        $this->assertDatabaseMigrationHas($this->table, 'invoke_down');
        $this->assertDatabaseMigrationHas($this->table, 'up_down');
        $this->assertDatabaseMigrationHas($table, 'up_down', column: 'value');
        $this->assertDatabaseMigrationHas($table, 'invoke_down', column: 'value');
        $this->assertDatabaseMigrationHas($table, 'invoke', column: 'value');
    }
}
