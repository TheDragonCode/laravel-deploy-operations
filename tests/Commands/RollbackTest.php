<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use Exception;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

class RollbackTest extends TestCase
{
    public function testRollbackCommand(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Make, ['name' => 'RollbackOne'])->assertExitCode(0);
        $this->artisan(Names::Make, ['name' => 'RollbackTwo'])->assertExitCode(0);

        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 2);

        $this->assertDatabaseOperationHas($this->table, 'rollback_one');
        $this->assertDatabaseOperationHas($this->table, 'rollback_two');
        $this->assertDatabaseOperationDoesntLike($this->table, 'rollback_tree');

        $this->artisan(Names::Rollback)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 2);

        $this->artisan(Names::Make, ['name' => 'RollbackTree'])->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 3);

        $this->assertDatabaseOperationHas($this->table, 'rollback_one');
        $this->assertDatabaseOperationHas($this->table, 'rollback_two');
        $this->assertDatabaseOperationHas($this->table, 'rollback_tree');
    }

    public function testEnvironment(): void
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_many_environments');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationHas($this->table, 'run_on_testing');
        $this->assertDatabaseOperationHas($this->table, 'run_on_many_environments');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->artisan(Names::Rollback)->assertExitCode(0);
        $this->assertDatabaseCount($table, 10);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_many_environments');
    }

    public function testDownSuccess(): void
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_success');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_success');

        $this->artisan(Names::Rollback)->assertExitCode(0);
        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_success');
    }

    public function testDownSuccessOnFailed(): void
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_success_on_failed');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_success_on_failed');

        try {
            $this->copySuccessFailureMethod();

            $this->table()->insert(['operation' => '2021_12_23_165048_run_success_on_failed', 'batch' => 999]);

            $this->assertDatabaseCount($table, 2);
            $this->assertDatabaseCount($this->table, 13);
            $this->assertDatabaseOperationHas($this->table, 'run_success_on_failed');

            $this->artisan(Names::Rollback)->assertExitCode(1);
        } catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_success_on_failed'));
        }

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 13);
        $this->assertDatabaseOperationHas($this->table, 'run_success_on_failed');
    }

    public function testDownFailed(): void
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_failed');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_failed');

        $this->artisan(Names::Rollback)->assertExitCode(0);
        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_failed');
    }

    public function testUpFailedOnException(): void
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_failed_failure');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_failed_failure');

        try {
            $this->copyFailedMethod();

            $this->table()->insert(['operation' => '2021_12_23_184029_run_failed_failure', 'batch' => 999]);

            $this->artisan(Names::Rollback)->assertExitCode(0);
        } catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_failed_failure'));
        }

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 13);
        $this->assertDatabaseOperationHas($this->table, 'run_failed_failure');
    }

    public function testDisabledBefore(): void
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationHas($this->table, 'test_before_disabled');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->artisan(Names::Rollback)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_disabled');
    }

    public function testEnabledBefore(): void
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::Operations, ['--before' => true])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseOperationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::Operations, ['--before' => true])->assertExitCode(0);

        $this->artisan(Names::Rollback)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_disabled');
    }

    public function testDI(): void
    {
        $this->copyDI();

        $table = 'test';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'invoke');
        $this->assertDatabaseOperationDoesntLike($this->table, 'invoke_down');
        $this->assertDatabaseOperationDoesntLike($this->table, 'up_down');
        $this->assertDatabaseOperationDoesntLike($table, 'up_down', column: 'value');
        $this->assertDatabaseOperationDoesntLike($table, 'invoke_down', column: 'value');
        $this->assertDatabaseOperationDoesntLike($table, 'invoke', column: 'value');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 3);
        $this->assertDatabaseOperationHas($this->table, 'invoke');
        $this->assertDatabaseOperationHas($this->table, 'invoke_down');
        $this->assertDatabaseOperationHas($this->table, 'up_down');
        $this->assertDatabaseOperationHas($table, 'up_down', column: 'value');
        $this->assertDatabaseOperationHas($table, 'invoke_down', column: 'value');
        $this->assertDatabaseOperationHas($table, 'invoke', column: 'value');

        $this->artisan(Names::Rollback)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'invoke');
        $this->assertDatabaseOperationDoesntLike($this->table, 'invoke_down');
        $this->assertDatabaseOperationDoesntLike($this->table, 'up_down');
        $this->assertDatabaseOperationDoesntLike($table, 'up_down', column: 'value');
        $this->assertDatabaseOperationDoesntLike($table, 'invoke_down', column: 'value');
        $this->assertDatabaseOperationHas($table, 'invoke', column: 'value');
    }
}
