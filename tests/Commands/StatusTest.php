<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class StatusTest extends TestCase
{
    public function testStatusCommand()
    {
        $this->assertFalse(
            Schema::hasTable($this->table)
        );

        $this->artisan('migrate:actions:install')->run();

        $this->assertTrue(
            Schema::hasTable($this->table)
        );

        $this->assertDatabaseCount($this->table, 0);

        if ($this->is6x()) {
            $this->artisan('migrate:actions:status')->run();
        } else {
            $this->artisan('migrate:actions:status')->expectsTable([], [])->run();
        }

        $filename = date('Y_m_d_His') . '_status';

        $this->artisan('make:migration:action', ['name' => 'Status'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan('migrate:actions:status')->run();

        $this->assertDatabaseHas($this->table, [
            'migration' => $filename,
            'batch'     => 1,
        ]);
    }
}
