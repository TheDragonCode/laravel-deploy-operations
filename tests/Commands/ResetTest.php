<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class ResetTest extends TestCase
{
    public function testResetCommand()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:install')->run();

        $this->assertTrue(
            Schema::hasTable($this->table)
        );

        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'Reset'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->assertTrue(
            $this->table()->whereRaw('migration like \'%reset\'')->exists()
        );

        $this->artisan('migrate:actions:reset')->run();

        $this->assertDatabaseCount($this->table, 0);

        $this->assertTrue(
            $this->table()->whereRaw('migration like \'%reset\'')->doesntExist()
        );
    }
}
