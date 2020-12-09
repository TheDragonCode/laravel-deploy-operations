<?php

namespace Tests\Commands;

use Tests\TestCase;

final class StatusTest extends TestCase
{
    public function testStatusCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        if ($this->is6x()) {
            $this->artisan('migrate:actions:status')->run();
        } else {
            $this->artisan('migrate:actions:status')->expectsTable([], [])->run();
        }

        $this->artisan('make:migration:action', ['name' => 'Status'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan('migrate:actions:status')->run();

        $this->assertDatabaseHasLike($this->table, 'migration', 'status');
    }
}
