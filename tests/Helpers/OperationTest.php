<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DragonCode\LaravelDeployOperations\Constants\Names;
use DragonCode\LaravelDeployOperations\Helpers\OperationHelper;
use Tests\TestCase;

class OperationTest extends TestCase
{
    public function testSuccess(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::Make, ['name' => 'TestMigration'])->assertExitCode(0);

        OperationHelper::run();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'test_migration');
    }

    public function testPath(): void
    {
        $this->copyFiles();

        $table = 'test';

        $path = 'sub_path/2021_12_15_205804_baz.php';

        $this->artisan(Names::Install)->assertExitCode(0);

        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseOperationDoesntLike($this->table, 'baz');

        OperationHelper::run($path);

        $this->assertDatabaseCount($table, 1);
        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseOperationHas($this->table, 'baz');

        $this->assertSame('sub_path/2021_12_15_205804_baz', $this->table()->first()->operation);
    }
}
