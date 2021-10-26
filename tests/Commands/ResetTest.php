<?php

namespace Tests\Commands;

use Tests\TestCase;

class ResetTest extends TestCase
{
    public function testResetCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'Reset'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'reset');

        $this->artisan('migrate:actions:reset')->run();

        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseMigrationDoesntLike($this->table, 'reset');
    }
}
