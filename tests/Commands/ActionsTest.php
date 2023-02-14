<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

class ActionsTest extends TestCase
{
    public function testActionsCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::MAKE, ['name' => 'TestMigration'])->assertExitCode(0);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseActionHas($this->table, 'test_migration');
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
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 2);
        $this->assertDatabaseActionHas($this->table, 'test_migration');
    }

    public function testOnce()
    {
        $this->copyFiles();

        $table = 'every_time';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
    }

    public function testSuccessTransaction()
    {
        $this->copySuccessTransaction();

        $table = 'transactions';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseActionHas($this->table, $table);
    }

    public function testFailedTransaction()
    {
        $this->copyFailedTransaction();

        $table = 'transactions';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, $table);

        try {
            $this->artisan(Names::ACTIONS)->assertExitCode(0);
        }
        catch (Exception $e) {
            $this->assertSame(Exception::class, get_class($e));
            $this->assertSame('Random message', $e->getMessage());
        }

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
    }

    public function testSingleEnvironment()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_testing');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionHas($this->table, 'run_on_testing');
        $this->assertDatabaseActionHas($this->table, 'run_except_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_testing');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionHas($this->table, 'run_on_testing');
        $this->assertDatabaseActionHas($this->table, 'run_except_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_testing');
    }

    public function testManyEnvironments()
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
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_many_environments');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionHas($this->table, 'run_on_testing');
        $this->assertDatabaseActionHas($this->table, 'run_on_many_environments');
        $this->assertDatabaseActionHas($this->table, 'run_except_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_many_environments');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_on_all');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseActionHas($this->table, 'run_on_testing');
        $this->assertDatabaseActionHas($this->table, 'run_on_many_environments');
        $this->assertDatabaseActionHas($this->table, 'run_except_production');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_except_many_environments');
    }

    public function testAllow()
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_allow');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_disallow');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_allow');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_disallow');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'run_allow');
        $this->assertDatabaseActionDoesntLike($this->table, 'run_disallow');
    }

    public function testUpSuccess()
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
    }

    public function testUpSuccessOnFailed()
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

            $this->artisan(Names::ACTIONS)->assertExitCode(0);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_success_on_failed'));
        }

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_success_on_failed');
    }

    public function testUpFailed()
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

            $this->artisan(Names::ACTIONS)->assertExitCode(0);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_failed_failure'));
        }

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionDoesntLike($this->table, 'run_failed_failure');
    }

    public function testPathAsFileWithExtension()
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path/2021_12_15_205804_baz.php';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'baz');
        $this->artisan(Names::ACTIONS, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseActionHas($this->table, 'baz');

        $this->assertSame('sub_path/2021_12_15_205804_baz', $this->table()->first()->action);
    }

    public function testPathAsFileWithoutExtension()
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path/2021_12_15_205804_baz';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'baz');
        $this->artisan(Names::ACTIONS, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseActionHas($this->table, 'baz');

        $this->assertSame($path, $this->table()->first()->action);
    }

    public function testPathAsDirectory()
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, 'baz');
        $this->artisan(Names::ACTIONS, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 2);
        $this->assertDatabaseActionHas($this->table, 'baz');

        $this->assertSame('sub_path/2021_12_15_205804_baz', $this->table()->first()->action);
    }

    public function testActionNotFound()
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
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionHas($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionHas($this->table, 'test_before_disabled');
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

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
    }

    public function testMixedBefore()
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

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionHas($this->table, 'test_before_disabled');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseActionHas($this->table, 'test_before_enabled');
        $this->assertDatabaseActionHas($this->table, 'test_before_disabled');
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
    }

    public function testSorting(): void
    {
        $files = [];

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $files[] = date('Y_m_d_His_') . 'test1';
        $this->artisan(Names::MAKE, ['name' => 'test1'])->assertExitCode(0);
        sleep(2);
        $files[] = 'foo/' . date('Y_m_d_His_') . 'test2';
        $this->artisan(Names::MAKE, ['name' => 'foo/test2'])->assertExitCode(0);
        sleep(2);
        $files[] = 'bar/' . date('Y_m_d_His_') . 'test3';
        $this->artisan(Names::MAKE, ['name' => 'bar/test3'])->assertExitCode(0);
        sleep(2);
        $files[] = 'foo/' . date('Y_m_d_His_') . 'test4';
        $this->artisan(Names::MAKE, ['name' => 'foo/test4'])->assertExitCode(0);
        sleep(2);
        $files[] = 'bar/' . date('Y_m_d_His_') . 'test5';
        $this->artisan(Names::MAKE, ['name' => 'bar/test5'])->assertExitCode(0);
        sleep(2);
        $files[] = date('Y_m_d_His_') . 'test6';
        $this->artisan(Names::MAKE, ['name' => 'test6'])->assertExitCode(0);

        $this->artisan(Names::ACTIONS)->assertExitCode(0);
        $this->assertDatabaseCount($this->table, 6);

        $records = DB::table($this->table)->orderBy('id')->pluck('action')->toArray();

        $this->assertSame($files, $records);
    }

    public function testDirectoryExclusion()
    {
        $this->copyFiles();

        $this->app['config']->set('actions.exclude', 'sub_path');

        $table = 'every_time';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
    }

    public function testFileExclusion()
    {
        $this->copyFiles();

        $this->app['config']->set('actions.exclude', 'sub_path/2021_12_15_205804_baz');

        $table = 'every_time';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionHas($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionHas($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionHas($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->assertDatabaseActionDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseActionHas($this->table, 'sub_path/2022_10_27_230732_foo');
    }

    public function testEmptyDirectory()
    {
        $this->copyEmptyDirectory();

        $table = 'every_time';

        $this->artisan(Names::INSTALL)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseActionDoesntLike($this->table, $table);
        $this->artisan(Names::ACTIONS)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
    }
}
