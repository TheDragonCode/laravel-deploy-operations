<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class RefreshTest extends TestCase
{
    public function testRefreshCommand()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:install')->run();

        $this->assertTrue(
            Schema::hasTable($this->table)
        );

        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'Refresh'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan('migrate:actions:refresh')->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->assertTrue(
            $this->table()->whereRaw('migration like \'%refresh\'')->exists()
        );
    }
}
