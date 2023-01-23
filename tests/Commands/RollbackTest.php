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

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'RollbackOne'])->assertExitCode(0);
        $this->artisan(Names::MAKE, ['name' => 'RollbackTwo'])->assertExitCode(0);

        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 2);

        $this->assertDatabaseActionHas($this->table, 'rollback_one');
        $this->assertDatabaseActionHas($this->table, 'rollback_two');
        $this->assertDatabaseActionDoesntLike($this->table, 'rollback_tree');

        $this->artisan(Names::ROLLBACK)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 2);

        $this->artisan(Names::MAKE, ['name' => 'RollbackTree'])->assertExitCode(0);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 3);

        $this->assertDatabaseActionHas($this->table, 'rollback_one');
        $this->assertDatabaseActionHas($this->table, 'rollback_two');
        $this->assertDatabaseActionHas($this->table, 'rollback_tree');
    }

    public function testEnvironment()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_many_environments');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionHas($this->table, 'run_on_testing');
        $this->assertDatabaseActionHas($this->table, 'run_on_many_environments');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->artisan(Names::ROLLBACK)->assertExitCode(0);
        $this->assertDatabaseCount($table, 10);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_many_environments');
    }

    public function testDownSuccess()
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_success');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_success');

        $this->artisan(Names::ROLLBACK)->assertExitCode(0);
        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_success');
    }

    public function testDownSuccessOnFailed()
    {
        $this->copyFiles();

        $table = 'success';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_success_on_failed');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_success_on_failed');

        try {
            $this->copySuccessFailureMethod();

            $this->table()->insert(['action' => '2021_12_23_165048_run_success_on_failed', 'batch' => 999]);

            $this->assertDatabaseCount($table, 2);
            $this->assertDatabaseCount($this->table, 13);
            $this->assertDatabaseActionHas($this->table, 'run_success_on_failed');

            $this->artisan(Names::ROLLBACK)->assertExitCode(1);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_success_on_failed'));
        }

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 13);
        $this->assertDatabaseActionHas($this->table, 'run_success_on_failed');
    }

    public function testDownFailed()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_failed');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_failed');

        $this->artisan(Names::ROLLBACK)->assertExitCode(0);
        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_failed');
    }

    public function testUpFailedOnException()
    {
        $this->copyFiles();

        $table = 'failed';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_failed_failure');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_failed_failure');

        try {
            $this->copyFailedMethod();

            $this->table()->insert(['action' => '2021_12_23_184029_run_failed_failure', 'batch' => 999]);

            $this->artisan(Names::ROLLBACK)->assertExitCode(0);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_failed_failure'));
        }

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 13);
        $this->assertDatabaseActionHas($this->table, 'run_failed_failure');
    }

    public function testDisabledBefore()
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionHas($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->artisan(Names::ROLLBACK)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
    }

    public function testEnabledBefore()
    {
        $this->copyFiles();

        $table = 'before';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS, ['--before' => true])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS, ['--before' => true])->assertExitCode(0);

        $this->artisan(Names::ROLLBACK)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
    }

    public function testDI(): void
    {
        $this->copyDI();

        $table = 'test';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'invoke');
        $this->assertDatabaseActionDoesntLike($this->table, 'invoke_down');
        $this->assertDatabaseActionDoesntLike($this->table, 'up_down');
        $this->assertDatabaseActionDoesntLike($table, 'up_down', column: 'value');
        $this->assertDatabaseActionDoesntLike($table, 'invoke_down', column: 'value');
        $this->assertDatabaseActionDoesntLike($table, 'invoke', column: 'value');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 3);
        $this->assertDatabaseActionHas($this->table, 'invoke');
        $this->assertDatabaseActionHas($this->table, 'invoke_down');
        $this->assertDatabaseActionHas($this->table, 'up_down');
        $this->assertDatabaseActionHas($table, 'up_down', column: 'value');
        $this->assertDatabaseActionHas($table, 'invoke_down', column: 'value');
        $this->assertDatabaseActionHas($table, 'invoke', column: 'value');

        $this->artisan(Names::ROLLBACK)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'invoke');
        $this->assertDatabaseActionDoesntLike($this->table, 'invoke_down');
        $this->assertDatabaseActionDoesntLike($this->table, 'up_down');
        $this->assertDatabaseActionDoesntLike($table, 'up_down', column: 'value');
        $this->assertDatabaseActionHas($table, 'invoke_down', column: 'value');
        $this->assertDatabaseActionHas($table, 'invoke', column: 'value');
    }
}
