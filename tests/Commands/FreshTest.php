<?php

declare(strict_types=1);

namespace Tests\Commands;

use Tests\TestCase;

class FreshTest extends TestCase
{
    public function testFreshCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan('migrate:actions:install')->run();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan('make:migration:action', ['name' => 'Fresh'])->run();
        $this->artisan('migrate:actions')->run();

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan('migrate:actions:fresh')->run();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseMigrationHas($this->table, 'fresh');
    }
}
