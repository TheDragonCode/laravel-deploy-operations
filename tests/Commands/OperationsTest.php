<?php

declare(strict_types=1);

namespace Tests\Commands;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Data\Config\ConfigData;
use DragonCode\LaravelDeployOperations\Jobs\OperationJob;
use Exception;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

class OperationsTest extends TestCase
{
    public function testSuccess(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Make, ['name' => 'TestMigration'])->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'test_migration');
    }

    public function testSameName(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Make, ['name' => 'TestMigration'])->assertExitCode(0);

        sleep(2);

        $this->artisan(Names::Make, ['name' => 'TestMigration'])->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($this->table, 2);
        $this->assertDatabaseOperationHas($this->table, 'test_migration');
    }

    public function testOnce(): void
    {
        $this->copyFiles();

        $table = 'every_time';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
    }

    public function testSuccessTransaction(): void
    {
        $this->copySuccessTransaction();

        $table = 'transactions';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, $table);
    }

    public function testFailedTransaction(): void
    {
        $this->copyFailedTransaction();

        $table = 'transactions';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);

        try {
            $this->artisan(Names::Operations)->assertExitCode(0);
        }
        catch (Exception $e) {
            $this->assertSame(Exception::class, get_class($e));
            $this->assertSame('Random message', $e->getMessage());
        }

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
    }

    public function testSingleEnvironment(): void
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_testing');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_testing');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationHas($this->table, 'run_on_testing');
        $this->assertDatabaseOperationHas($this->table, 'run_except_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_testing');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationHas($this->table, 'run_on_testing');
        $this->assertDatabaseOperationHas($this->table, 'run_except_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_testing');
    }

    public function testManyEnvironments(): void
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
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_many_environments');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationHas($this->table, 'run_on_testing');
        $this->assertDatabaseOperationHas($this->table, 'run_on_many_environments');
        $this->assertDatabaseOperationHas($this->table, 'run_except_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_many_environments');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_on_all');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_on_production');
        $this->assertDatabaseOperationHas($this->table, 'run_on_testing');
        $this->assertDatabaseOperationHas($this->table, 'run_on_many_environments');
        $this->assertDatabaseOperationHas($this->table, 'run_except_production');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_testing');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_except_many_environments');
    }

    public function testAllow(): void
    {
        $this->copyFiles();

        $table = 'environment';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_allow');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_disallow');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_allow');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_disallow');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 5);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'run_allow');
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_disallow');
    }

    public function testUpSuccess(): void
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
    }

    public function testUpSuccessOnFailed(): void
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

            $this->artisan(Names::Operations)->assertExitCode(0);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_success_on_failed'));
        }

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_success_on_failed');
    }

    public function testUpFailed(): void
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

            $this->artisan(Names::Operations)->assertExitCode(0);
        }
        catch (Throwable $e) {
            $this->assertInstanceOf(Exception::class, $e);

            $this->assertSame('Custom exception', $e->getMessage());

            $this->assertTrue(Str::contains($e->getFile(), 'run_failed_failure'));
        }

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationDoesntLike($this->table, 'run_failed_failure');
    }

    public function testPathAsFileWithExtension(): void
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path/2021_12_15_205804_baz.php';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'baz');
        $this->artisan(Names::Operations, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'baz');

        $this->assertSame('sub_path/2021_12_15_205804_baz', $this->table()->first()->operation);
    }

    public function testPathAsFileWithoutExtension(): void
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path/2021_12_15_205804_baz';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'baz');
        $this->artisan(Names::Operations, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'baz');

        $this->assertSame($path, $this->table()->first()->operation);
    }

    public function testPathAsDirectory(): void
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'baz');
        $this->artisan(Names::Operations, ['--path' => $path])->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 2);
        $this->assertDatabaseOperationHas($this->table, 'baz');

        $this->assertSame('sub_path/2021_12_15_205804_baz', $this->table()->first()->operation);
    }

    public function testOperationsNotFound(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);

        $this->artisan(Names::Status)->assertExitCode(0);
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

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationHas($this->table, 'test_before_disabled');
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

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseOperationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_disabled');
    }

    public function testMixedBefore(): void
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

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseOperationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationDoesntLike($this->table, 'test_before_disabled');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationHas($this->table, 'test_before_disabled');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 12);
        $this->assertDatabaseOperationHas($this->table, 'test_before_enabled');
        $this->assertDatabaseOperationHas($this->table, 'test_before_disabled');
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
    }

    public function testSorting(): void
    {
        $files = [];

        $this->artisan(Names::Install)->assertExitCode(0);

        $files[] = date('Y_m_d_His_') . 'test1';
        $this->artisan(Names::Make, ['name' => 'test1'])->assertExitCode(0);
        sleep(2);
        $files[] = 'foo/' . date('Y_m_d_His_') . 'test2';
        $this->artisan(Names::Make, ['name' => 'foo/test2'])->assertExitCode(0);
        sleep(2);
        $files[] = 'bar/' . date('Y_m_d_His_') . 'test3';
        $this->artisan(Names::Make, ['name' => 'bar/test3'])->assertExitCode(0);
        sleep(2);
        $files[] = 'foo/' . date('Y_m_d_His_') . 'test4';
        $this->artisan(Names::Make, ['name' => 'foo/test4'])->assertExitCode(0);
        sleep(2);
        $files[] = 'bar/' . date('Y_m_d_His_') . 'test5';
        $this->artisan(Names::Make, ['name' => 'bar/test5'])->assertExitCode(0);
        sleep(2);
        $files[] = date('Y_m_d_His_') . 'test6';
        $this->artisan(Names::Make, ['name' => 'test6'])->assertExitCode(0);

        $this->artisan(Names::Operations)->assertExitCode(0);
        $this->assertDatabaseCount($this->table, 6);

        $records = DB::table($this->table)->orderBy('id')->pluck('operation')->all();

        $this->assertSame($files, $records);
    }

    public function testDirectoryExclusion(): void
    {
        $this->copyFiles();

        $this->app['config']->set('deploy-operations.exclude', 'sub_path');

        $this->app->forgetInstance(ConfigData::class);

        $table = 'every_time';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 10);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
    }

    public function testFileExclusion(): void
    {
        $this->copyFiles();

        $this->app['config']->set('deploy-operations.exclude', 'sub_path/2021_12_15_205804_baz');

        $this->app->forgetInstance(ConfigData::class);

        $table = 'every_time';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationHas($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationHas($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 3);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationHas($this->table, 'sub_path/2022_10_27_230732_foo');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 4);
        $this->assertDatabaseCount($this->table, 11);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->assertDatabaseOperationDoesntLike($this->table, 'sub_path/2021_12_15_205804_baz');
        $this->assertDatabaseOperationHas($this->table, 'sub_path/2022_10_27_230732_foo');
    }

    public function testEmptyDirectory(): void
    {
        $this->copyEmptyDirectory();

        $table = 'every_time';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, $table);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
    }

    public function testAsync(): void
    {
        $this->copyAsync();

        $table1 = 'test';
        $table2 = 'every_time';

        $queue = config('deploy-operations.queue.name');

        Queue::fake();

        $this->artisan(Names::Install)->assertExitCode(0);

        Queue::assertNothingPushed();

        $this->assertDatabaseCount($table1, 0);
        $this->assertDatabaseCount($table2, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'foo_bar');
        $this->assertDatabaseOperationDoesntLike($this->table, 'every_time');

        $this->artisan(Names::Operations)->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseOperationDoesntLike($this->table, 'foo_bar');
        $this->assertDatabaseOperationDoesntLike($this->table, 'every_time');

        Queue::assertPushed(OperationJob::class, 2);

        Queue::assertPushedOn($queue, OperationJob::class, fn (OperationJob $job) => Str::contains($job->filename, [
            'foo_bar',
            'every_time',
        ]));
    }

    public function testSync(): void
    {
        $this->copyAsync();

        $table1 = 'test';
        $table2 = 'every_time';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table1, 0);
        $this->assertDatabaseCount($table2, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'foo_bar');
        $this->assertDatabaseOperationDoesntLike($this->table, 'every_time');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table1, 1);
        $this->assertDatabaseCount($table2, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'foo_bar');
        $this->assertDatabaseOperationDoesntLike($this->table, 'every_time');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table1, 1);
        $this->assertDatabaseCount($table2, 2);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'foo_bar');
        $this->assertDatabaseOperationDoesntLike($this->table, 'every_time');
        $this->artisan(Names::Operations)->assertExitCode(0);

        $this->assertDatabaseCount($table1, 1);
        $this->assertDatabaseCount($table2, 3);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'foo_bar');
        $this->assertDatabaseOperationDoesntLike($this->table, 'every_time');
    }

    public function testViaMigrationMethod(): void
    {
        $this->copyViaMigrations();

        $table = 'test';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'custom');
        $this->assertDatabaseOperationDoesntLike($this->table, 'invoke');
        $this->assertDatabaseOperationDoesntLike($this->table, 'up_down');
        $this->assertDatabaseOperationDoesntLike($table, 'custom', column: 'value');
        $this->assertDatabaseOperationDoesntLike($table, 'invoke', column: 'value');
        $this->assertDatabaseOperationDoesntLike($table, 'up_down', column: 'value');

        $this->loadMigrationsFrom(__DIR__ . '/../fixtures/migrations_with_operations');

        $this->assertDatabaseCount($table, 2);
        $this->assertDatabaseCount($this->table, 2);
        $this->assertDatabaseOperationDoesntLike($this->table, 'custom');
        $this->assertDatabaseOperationHas($this->table, 'invoke');
        $this->assertDatabaseOperationHas($this->table, 'up_down');
        $this->assertDatabaseOperationDoesntLike($table, 'custom', column: 'value');
        $this->assertDatabaseOperationHas($table, 'invoke', column: 'value');
        $this->assertDatabaseOperationHas($table, 'up_down', column: 'value');

        $this->artisan(RollbackCommand::class, [
            '--path'     => __DIR__ . '/../fixtures/migrations_with_operations',
            '--realpath' => true,
        ])->assertSuccessful();

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationDoesntLike($this->table, 'custom');
        $this->assertDatabaseOperationHas($this->table, 'invoke');
        $this->assertDatabaseOperationDoesntLike($this->table, 'up_down');
        $this->assertDatabaseOperationDoesntLike($table, 'custom', column: 'value');
        $this->assertDatabaseOperationHas($table, 'invoke', column: 'value');
        $this->assertDatabaseOperationDoesntLike($table, 'up_down', column: 'value');
    }
}
