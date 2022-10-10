<?php

namespace Tests\Commands;

use DragonCode\LaravelActions\Constants\Names;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::STATUS)->assertSuccessful();

        $this->assertDatabaseDoesntTable($this->table);
    }

    public function testStatusCommand()
    {
        $this->assertDatabaseDoesntTable($this->table);

        $this->artisan(Names::INSTALL)->assertSuccessful();

        $this->assertDatabaseHasTable($this->table);
        $this->assertDatabaseCount($this->table, 0);

        $this->artisan(Names::STATUS)->expectsTable([], [])->assertSuccessful();

        $this->artisan(Names::MAKE, ['name' => 'Status'])->assertSuccessful();
        $this->artisan(Names::MIGRATE)->assertSuccessful();

        $this->assertDatabaseCount($this->table, 1);

        $this->artisan(Names::STATUS)->assertSuccessful();

        $this->assertDatabaseMigrationHas($this->table, 'status');
    }
}
