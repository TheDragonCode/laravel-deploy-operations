<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class MigrateTest extends TestCase
{
    public function testMigrationCommand()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:install')->run();

        $this->assertTrue(
            Schema::hasTable($this->table)
        );

        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'TestMigration'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->assertTrue(
            $this->table()->whereRaw('migration like \'%test_migration\'')->exists()
        );
    }

    public function testMigrationNotFound()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:install')->run();

        $this->assertTrue(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:status')
            ->expectsOutput('No actions found');
    }
}
